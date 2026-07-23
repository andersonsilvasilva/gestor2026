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

    public function test_cannot_create_a_cliente_with_a_duplicate_cpf(): void
    {
        $user = User::factory()->rh()->create();
        $existente = Client::factory()->create();

        $this->actingAs($user)
            ->post('/clientes/create-cliente', $this->dadosCliente(['cpf' => $existente->cpf]))
            ->assertSessionHasErrors('cpf');
    }

    public function test_cannot_create_a_cliente_with_a_duplicate_cns(): void
    {
        $user = User::factory()->rh()->create();
        $existente = Client::factory()->create();

        $this->actingAs($user)
            ->post('/clientes/create-cliente', $this->dadosCliente(['cns' => $existente->cns]))
            ->assertSessionHasErrors('cns');
    }

    public function test_can_create_a_cliente_without_a_cns(): void
    {
        $user = User::factory()->rh()->create();

        $this->actingAs($user)
            ->post('/clientes/create-cliente', $this->dadosCliente(['cns' => null]))
            ->assertRedirect(route('clientes'));
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

    public function test_update_validation_failure_redisplays_the_form_without_crashing(): void
    {
        $user = User::factory()->rh()->create();
        $cliente = Client::factory()->create();

        // omite 'nome' (obrigatório) pra forçar a validação a falhar e cair
        // no bloco @if($errors->any()) do formulário de edição
        $dados = $this->dadosCliente();
        unset($dados['nome']);

        $response = $this->actingAs($user)
            ->from(route('cliente.edita-cliente', ['id' => Crypt::encrypt($cliente->id)]))
            ->post('/clientes/update-cliente/' . Crypt::encrypt($cliente->id), $dados);

        $response->assertSessionHasErrors('nome');

        // a asserção real é que isto não derruba a página com um 500 (era o
        // bug: {{ errors }} em vez de {{ $error }} disparava um erro fatal
        // de "constante indefinida" assim que $errors->any() era verdadeiro)
        $edit = $this->actingAs($user)->get(route('cliente.edita-cliente', ['id' => Crypt::encrypt($cliente->id)]));
        $edit->assertOk();
        $edit->assertSee('alert-danger', false);
    }

    public function test_create_validation_failure_keeps_select_choices_on_redisplay(): void
    {
        $user = User::factory()->rh()->create();

        $dados = $this->dadosCliente(['sexo' => 'FEMININO', 'cor' => 'PARDA', 'uf' => 'MT']);
        unset($dados['nome']); // força a validação a falhar (nome é obrigatório)

        $this->actingAs($user)
            ->from(route('clientes.new-cliente'))
            ->post('/clientes/create-cliente', $dados)
            ->assertSessionHasErrors('nome');

        $form = $this->actingAs($user)->get(route('clientes.new-cliente'));
        $form->assertOk();

        $html = $form->getContent();
        $this->assertMatchesRegularExpression('/<option value="FEMININO"\s+selected[^>]*>/', $html);
        $this->assertMatchesRegularExpression('/<option value="PARDA"\s+selected[^>]*>/', $html);
        $this->assertMatchesRegularExpression('/<option value="MT"\s+selected[^>]*>/', $html);
    }

    public function test_edit_form_preselects_the_clientes_current_values(): void
    {
        $user = User::factory()->rh()->create();
        $cliente = Client::factory()->create(['sexo' => 'FEMININO', 'cor' => 'INDIGINA', 'uf' => 'SP']);

        $response = $this->actingAs($user)->get(route('cliente.edita-cliente', ['id' => Crypt::encrypt($cliente->id)]));

        $response->assertOk();
        $html = $response->getContent();
        $this->assertMatchesRegularExpression('/selected[^>]*\s+value="FEMININO"/', $html);
        $this->assertMatchesRegularExpression('/selected[^>]*\s+value="INDIGINA"/', $html);
        $this->assertMatchesRegularExpression('/selected[^>]*\s+value="SP"/', $html);
    }

    public function test_update_validation_failure_keeps_select_choices_on_redisplay(): void
    {
        $user = User::factory()->rh()->create();
        $cliente = Client::factory()->create(['sexo' => 'MASCULINO', 'cor' => 'BRANCA', 'uf' => 'MT']);

        $dados = $this->dadosCliente(['sexo' => 'FEMININO', 'cor' => 'PARDA', 'uf' => 'SP']);
        unset($dados['nome']); // força a validação a falhar (nome é obrigatório)

        $editUrl = route('cliente.edita-cliente', ['id' => Crypt::encrypt($cliente->id)]);

        $this->actingAs($user)
            ->from($editUrl)
            ->post('/clientes/update-cliente/' . Crypt::encrypt($cliente->id), $dados)
            ->assertSessionHasErrors('nome');

        $edit = $this->actingAs($user)->get($editUrl);
        $edit->assertOk();

        $html = $edit->getContent();
        // deve refletir o que foi tentado salvar (FEMININO/PARDA/SP), não o
        // que já estava no banco (MASCULINO/BRANCA/MT)
        $this->assertMatchesRegularExpression('/selected[^>]*\s+value="FEMININO"/', $html);
        $this->assertMatchesRegularExpression('/selected[^>]*\s+value="PARDA"/', $html);
        $this->assertMatchesRegularExpression('/selected[^>]*\s+value="SP"/', $html);
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

        $response = $this->actingAs($user)->get('/clientes?busca=Maria');

        $response->assertOk();
        $this->assertTrue($response->viewData('clientes')->contains('nome', 'Maria da Silva'));
        $this->assertFalse($response->viewData('clientes')->contains('nome', 'João Pereira'));
    }

    public function test_search_by_cpf_filters_the_cliente_list(): void
    {
        $user = User::factory()->rh()->create();
        $alvo = Client::factory()->create(['nome' => 'Maria da Silva', 'cpf' => '111.222.333-44']);
        Client::factory()->create(['nome' => 'João Pereira', 'cpf' => '555.666.777-88']);

        $response = $this->actingAs($user)->get('/clientes?busca=111.222.333-44');

        $response->assertOk();
        $this->assertTrue($response->viewData('clientes')->contains('id', $alvo->id));
        $this->assertSame(1, $response->viewData('clientes')->count());
    }

    public function test_search_by_cpf_ignores_punctuation(): void
    {
        $user = User::factory()->rh()->create();
        $alvo = Client::factory()->create(['nome' => 'Maria da Silva', 'cpf' => '111.222.333-44']);

        // busca só com os números, sem pontuação, ainda deve encontrar o cliente
        $response = $this->actingAs($user)->get('/clientes?busca=11122233344');

        $response->assertOk();
        $this->assertTrue($response->viewData('clientes')->contains('id', $alvo->id));
    }

    public function test_search_by_telefone_filters_the_cliente_list(): void
    {
        $user = User::factory()->rh()->create();
        $alvo = Client::factory()->create(['nome' => 'Maria da Silva', 'telefone' => '(65) 99999-1234']);
        Client::factory()->create(['nome' => 'João Pereira', 'telefone' => '(65) 98888-5678']);

        $response = $this->actingAs($user)->get('/clientes?busca=999991234');

        $response->assertOk();
        $this->assertTrue($response->viewData('clientes')->contains('id', $alvo->id));
    }
}
