<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Cobre a validação e a autorização de `ControllerReport::generateReport`.
 * Os dois testes de "sucesso" (que passam da validação/autorização) usam
 * Http::fake() para simular a resposta do serviço de relatórios (VPS
 * externo, que roda o motor JasperReports/Java) — o motor real usa o banco
 * de produção, então não deve ser acionado durante os testes.
 */
class ReportGeneratorAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_report_requires_all_fields(): void
    {
        $user = User::factory()->rh()->create();

        $this->actingAs($user)->post('/gerar', [])
            ->assertSessionHasErrors(['q_relatorio', 'id_colaborador', 'dta_inicio', 'dta_fim']);
    }

    public function test_end_date_must_be_after_or_equal_to_start_date(): void
    {
        $user = User::factory()->rh()->create();

        $this->actingAs($user)->post('/gerar', [
            'q_relatorio'    => 'bpa1',
            'id_colaborador' => $user->id,
            'dta_inicio'     => '2026-02-01',
            'dta_fim'        => '2026-01-01',
        ])->assertSessionHasErrors('dta_fim');
    }

    public function test_rh_cannot_generate_a_report_for_another_colaborador(): void
    {
        $user = User::factory()->rh()->create();
        $outro = User::factory()->rh()->create();

        $this->actingAs($user)->post('/gerar', [
            'q_relatorio'    => 'bpa1',
            'id_colaborador' => $outro->id,
            'dta_inicio'     => '2026-01-01',
            'dta_fim'        => '2026-01-31',
        ])->assertForbidden();
    }

    public function test_rh_can_pass_validation_and_authorization_for_their_own_report(): void
    {
        Http::fake(['*/gerar' => Http::response('%PDF-fake%', 200, ['Content-Type' => 'application/pdf'])]);

        $user = User::factory()->rh()->create();

        $response = $this->actingAs($user)->post('/gerar', [
            'q_relatorio'    => 'bpa1',
            'id_colaborador' => $user->id,
            'dta_inicio'     => '2026-01-01',
            'dta_fim'        => '2026-01-31',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertOk();
        Http::assertSent(fn ($request) => str_contains($request->url(), '/gerar'));
    }

    public function test_admin_rt_and_sec_can_generate_a_report_for_any_colaborador(): void
    {
        Http::fake(['*/gerar' => Http::response('%PDF-fake%', 200, ['Content-Type' => 'application/pdf'])]);

        $colaborador = User::factory()->rh()->create();

        foreach (['admin', 'rt', 'sec'] as $role) {
            $user = User::factory()->{$role}()->create();

            $response = $this->actingAs($user)->post('/gerar', [
                'q_relatorio'    => 'bpa1',
                'id_colaborador' => $colaborador->id,
                'dta_inicio'     => '2026-01-01',
                'dta_fim'        => '2026-01-31',
            ]);

            $this->assertNotEquals(403, $response->getStatusCode());
            $response->assertOk();
        }
    }
}
