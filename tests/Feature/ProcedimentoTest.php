<?php

namespace Tests\Feature;

use App\Models\Procediment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcedimentoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_procedimento(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/procedimento/create-procedimento', [
            'codigo' => 'PROC01',
            'name'   => 'Acolhimento',
        ])->assertRedirect(route('procedimentos'));

        $this->assertDatabaseHas('procediments', ['codigo' => 'PROC01', 'name' => 'Acolhimento']);
    }

    public function test_cannot_create_a_procedimento_with_a_duplicate_codigo(): void
    {
        $admin = User::factory()->admin()->create();
        Procediment::factory()->create(['codigo' => 'DUPLICADO']);

        $this->actingAs($admin)->post('/procedimento/create-procedimento', [
            'codigo' => 'DUPLICADO',
            'name'   => 'Outro Nome',
        ])->assertSessionHasErrors('codigo');
    }

    public function test_admin_can_update_a_procedimento(): void
    {
        $admin = User::factory()->admin()->create();
        $procedimento = Procediment::factory()->create(['name' => 'Nome Antigo', 'ativo' => '1']);

        $this->actingAs($admin)->post('/procedimento/update-procedimento', [
            'id'     => $procedimento->id,
            'codigo' => $procedimento->codigo,
            'name'   => 'Nome Novo',
            'ativo'  => true,
        ])->assertRedirect(route('procedimentos'));

        $this->assertDatabaseHas('procediments', ['id' => $procedimento->id, 'name' => 'Nome Novo']);
    }

    public function test_deleting_a_procedimento_requires_post(): void
    {
        $admin = User::factory()->admin()->create();
        $procedimento = Procediment::factory()->create();

        $this->actingAs($admin)->get("/procedimento/delete-procedimento-confirm/{$procedimento->id}")->assertMethodNotAllowed();

        $this->actingAs($admin)->post("/procedimento/delete-procedimento-confirm/{$procedimento->id}")
            ->assertRedirect(route('procedimentos'));

        // procediments não tem soft delete (a migration nunca criou deleted_at
        // nessa tabela) — é exclusão definitiva mesmo.
        $this->assertDatabaseMissing('procediments', ['id' => $procedimento->id]);
    }

    /**
     * Procedimentos 1 e 2 são protegidos (usados como referência fixa) e não
     * podem ser editados/excluídos por ninguém, nem direto via POST na
     * confirmação (sem passar pela tela que mostra o cadeado).
     */
    public function test_procedimentos_one_and_two_are_protected_from_update_and_delete(): void
    {
        $admin = User::factory()->admin()->create();

        foreach ([1, 2] as $id) {
            Procediment::query()->forceCreate(['id' => $id, 'codigo' => "SEED{$id}", 'name' => "Seed {$id}", 'ativo' => true]);
        }

        foreach ([1, 2] as $id) {
            $this->actingAs($admin)->post('/procedimento/update-procedimento', [
                'id' => $id, 'codigo' => 'X', 'name' => 'Nao deveria mudar', 'ativo' => true,
            ])->assertRedirect(route('procedimentos'));
            $this->assertDatabaseMissing('procediments', ['id' => $id, 'name' => 'Nao deveria mudar']);

            $this->actingAs($admin)->post("/procedimento/delete-procedimento-confirm/{$id}")
                ->assertRedirect(route('procedimentos'));
            $this->assertDatabaseHas('procediments', ['id' => $id]);

            $this->actingAs($admin)->post("/procedimento/toggle-status/{$id}")
                ->assertRedirect(route('procedimentos'));
            $this->assertDatabaseHas('procediments', ['id' => $id, 'ativo' => true]);
        }
    }

    public function test_admin_can_inactivate_and_reactivate_a_procedimento(): void
    {
        $admin = User::factory()->admin()->create();
        $procedimento = Procediment::factory()->create(['ativo' => true]);

        $this->actingAs($admin)->post("/procedimento/toggle-status/{$procedimento->id}")
            ->assertRedirect(route('procedimentos'));
        $this->assertFalse($procedimento->refresh()->ativo);

        $this->actingAs($admin)->post("/procedimento/toggle-status/{$procedimento->id}")
            ->assertRedirect(route('procedimentos'));
        $this->assertTrue($procedimento->refresh()->ativo);
    }

    public function test_inactive_procedimento_is_excluded_from_the_agenda_selector(): void
    {
        $user = User::factory()->rh()->create();
        $cliente = \App\Models\Client::factory()->create();
        $ativo = Procediment::factory()->create(['ativo' => true, 'name' => 'Procedimento Ativo']);
        $inativo = Procediment::factory()->create(['ativo' => false, 'name' => 'Procedimento Inativo']);

        $response = $this->actingAs($user)->get('/agenda/index/' . \Illuminate\Support\Facades\Crypt::encrypt($cliente->id));

        $response->assertOk();
        $this->assertTrue($response->viewData('procedimentos')->contains('id', $ativo->id));
        $this->assertFalse($response->viewData('procedimentos')->contains('id', $inativo->id));
    }

    public function test_rt_can_manage_procedimentos_but_sec_and_rh_cannot(): void
    {
        $rt = User::factory()->rt()->create();
        $this->actingAs($rt)->post('/procedimento/create-procedimento', ['codigo' => 'RT01', 'name' => 'Auriculoterapia'])
            ->assertRedirect(route('procedimentos'));

        $sec = User::factory()->sec()->create();
        $this->actingAs($sec)->post('/procedimento/create-procedimento', ['codigo' => 'SEC01', 'name' => 'Nao Deveria'])
            ->assertForbidden();

        $rh = User::factory()->rh()->create();
        $this->actingAs($rh)->post('/procedimento/create-procedimento', ['codigo' => 'RH01', 'name' => 'Nao Deveria'])
            ->assertForbidden();
    }
}
