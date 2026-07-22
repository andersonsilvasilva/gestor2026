<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Cobre a criação/edição de colaborador (RhUserController), em especial a
 * troca de identificador do SUS de CNS para CPF: CPF passa a ser
 * obrigatório/único, CNS vira opcional.
 */
class RhColaboradorTest extends TestCase
{
    use RefreshDatabase;

    private function dadosColaborador(array $overrides = []): array
    {
        return array_merge([
            'name'              => 'Colaborador Teste',
            'email'             => fake()->unique()->safeEmail(),
            'select_department' => Department::factory()->create()->id,
            'endereco'          => 'Rua Teste',
            'bairro'            => 'Centro',
            'cep'               => '78000-000',
            'cidade'            => 'Cuiabá',
            'telefone'          => '(65) 99999-9999',
            'cpf'               => (string) fake()->unique()->numerify('###########'),
            'cbo'               => '223565',
            'data_nascimento'   => '1990-01-01',
            'comentario'        => 'Sem observações.',
            'salario'           => '1500.00',
            'admission_date'    => '2020-01-01',
        ], $overrides);
    }

    public function test_manage_catalog_can_create_a_colaborador_with_cpf(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('colaborators.rh.create-colaborator'), $this->dadosColaborador(['email' => 'novo@teste.com']))
            ->assertRedirect(route('colaborators.rh-users'));

        $user = User::where('email', 'novo@teste.com')->firstOrFail();
        $this->assertDatabaseHas('user_details', ['user_id' => $user->id, 'cpf' => $user->detail->cpf]);
    }

    public function test_cannot_create_a_colaborador_without_cpf(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('colaborators.rh.create-colaborator'), $this->dadosColaborador(['cpf' => '']))
            ->assertSessionHasErrors('cpf');
    }

    public function test_cannot_create_a_colaborador_with_a_duplicate_cpf(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $existente = User::factory()->rh()->create();
        $existente->detail()->create([
            'endereco' => 'Rua X', 'bairro' => 'Bairro X', 'cep' => '78000-000', 'cidade' => 'Cuiabá',
            'telefone' => '(65) 90000-0000', 'cpf' => '111.111.111-11', 'cbo' => '223565',
            'data_nascimento' => '1990-01-01', 'comentario' => '', 'salario' => '1500.00', 'admission_date' => '2020-01-01',
        ]);

        $this->actingAs($admin)
            ->post(route('colaborators.rh.create-colaborator'), $this->dadosColaborador(['cpf' => '111.111.111-11']))
            ->assertSessionHasErrors('cpf');
    }

    public function test_can_create_a_colaborador_without_a_cns(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('colaborators.rh.create-colaborator'), $this->dadosColaborador(['email' => 'semcns@teste.com']))
            ->assertRedirect(route('colaborators.rh-users'));

        $user = User::where('email', 'semcns@teste.com')->firstOrFail();
        $this->assertNull($user->detail->cns);
    }

    public function test_can_update_a_colaborador_cpf(): void
    {
        Mail::fake();
        $admin = User::factory()->admin()->create();
        $colaborador = User::factory()->rh()->create();
        $colaborador->detail()->create([
            'endereco' => 'Rua X', 'bairro' => 'Bairro X', 'cep' => '78000-000', 'cidade' => 'Cuiabá',
            'telefone' => '(65) 90000-0000', 'cpf' => '222.222.222-22', 'cbo' => '223565',
            'data_nascimento' => '1990-01-01', 'comentario' => '', 'salario' => '1500.00', 'admission_date' => '2020-01-01',
        ]);

        $this->actingAs($admin)->post(route('colaborators.rh.update-colaborator'), [
            'id'              => $colaborador->id,
            'telefone'        => '(65) 98888-8888',
            'salario'         => '2000.00',
            'data_nascimento' => '1990-01-01',
            'cpf'             => '333.333.333-33',
            'cbo'             => '223565',
            'admission_date'  => '2020-01-01',
        ])->assertRedirect(route('colaborators.rh-users'));

        $this->assertDatabaseHas('user_details', ['user_id' => $colaborador->id, 'cpf' => '333.333.333-33']);
    }
}
