<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Dados enviados pelo formulário de criação/edição de cliente. Inclui
     * também campos que `ContaRequest` não exige mas que
     * `ClienteController::updateCliente` lê diretamente do request (o
     * formulário real sempre os envia, pré-preenchidos).
     */
    private function dadosCliente(array $overrides = []): array
    {
        return array_merge([
            'cns'             => (string) fake()->unique()->numerify('####################'),
            'nome'            => 'Cliente Teste',
            'sexo'            => 'Feminino',
            'data_nascimento' => '1990-01-01',
            'nacionalidade'   => 'Brasileira',
            'cpf'             => (string) fake()->unique()->numerify('###########'),
            'cor'             => 'Parda',
            'etnia'           => 'Não informada',
            'cep'             => '78000-000',
            'logradouro'      => 'Rua',
            'endereco'        => 'Rua Teste',
            'numero'          => '123',
            'telefone'        => '(65) 99999-9999',
            'estado_civil'    => 'Solteiro',
            'bairro'          => 'Centro',
            'cidade'          => 'Cuiabá',
            'uf'              => 'MT',
            'email'           => fake()->unique()->safeEmail(),
            'tipo_sangue'     => 'O+',
            'ativ_laboral'    => 'Nenhuma',
            'observacao'      => '',
        ], $overrides);
    }

    public function test_authenticated_user_can_list_clientes(): void
    {
        $user = User::factory()->rh()->create();
        Client::factory()->count(3)->create();

        $this->actingAs($user)->get('/clientes')->assertOk();
    }

    public function test_can_create_a_cliente_with_valid_data(): void
    {
        $user = User::factory()->rh()->create();

        $this->actingAs($user)->post('/clientes/create-cliente', $this->dadosCliente(['nome' => 'Fulano de Tal']))
            ->assertRedirect(route('clientes'));

        $this->assertDatabaseHas('clients', ['nome' => 'Fulano de Tal']);
    }

    public function test_cannot_create_a_cliente_with_a_duplicate_cns(): void
    {
        $user = User::factory()->rh()->create();
        $existente = Client::factory()->create();

        $this->actingAs($user)
            ->post('/clientes/create-cliente', $this->dadosCliente(['cns' => $existente->cns]))
            ->assertSessionHasErrors('cns');
    }

    public function test_can_update_a_cliente(): void
    {
        $user = User::factory()->rh()->create();
        $cliente = Client::factory()->create(['nome' => 'Nome Antigo']);

        $this->actingAs($user)->post('/clientes/update-cliente/' . Crypt::encrypt($cliente->id), $this->dadosCliente([
            'nome' => 'Nome Novo',
        ]))->assertRedirect(route('clientes'));

        $this->assertDatabaseHas('clients', ['id' => $cliente->id, 'nome' => 'Nome Novo']);
    }

    public function test_deleting_a_cliente_is_a_soft_delete_via_post(): void
    {
        $user = User::factory()->rh()->create();
        $cliente = Client::factory()->create();
        $crypt = Crypt::encrypt($cliente->id);

        $this->actingAs($user)->get("/clientes/delete-cliente_confirm/{$crypt}")->assertMethodNotAllowed();

        $this->actingAs($user)->post("/clientes/delete-cliente_confirm/{$crypt}")
            ->assertRedirect(route('clientes'));

        $this->assertSoftDeleted('clients', ['id' => $cliente->id]);
    }

    public function test_restoring_a_cliente_requires_post_and_clears_deleted_at(): void
    {
        $user = User::factory()->rh()->create();
        $cliente = Client::factory()->create();
        $cliente->delete();
        $crypt = Crypt::encrypt($cliente->id);

        $this->actingAs($user)->get("/clientes/restore/{$crypt}")->assertMethodNotAllowed();

        $this->actingAs($user)->post("/clientes/restore/{$crypt}")
            ->assertRedirect(route('clientes'));

        $this->assertDatabaseHas('clients', ['id' => $cliente->id, 'deleted_at' => null]);
    }

    public function test_search_by_name_filters_the_cliente_list(): void
    {
        $user = User::factory()->rh()->create();
        Client::factory()->create(['nome' => 'Maria da Silva']);
        Client::factory()->create(['nome' => 'João Pereira']);

        $response = $this->actingAs($user)->get('/clientes?nome=Maria');

        $response->assertOk();
        $this->assertTrue($response->viewData('clientes')->contains('nome', 'Maria da Silva'));
        $this->assertFalse($response->viewData('clientes')->contains('nome', 'João Pereira'));
    }
}
