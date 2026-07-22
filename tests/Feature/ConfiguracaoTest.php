<?php

namespace Tests\Feature;

use App\Models\Configuracao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ConfiguracaoTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_view_configuracoes(): void
    {
        $this->actingAs(User::factory()->rt()->create())->get('/configuracoes')->assertForbidden();
        $this->actingAs(User::factory()->sec()->create())->get('/configuracoes')->assertForbidden();
        $this->actingAs(User::factory()->rh()->create())->get('/configuracoes')->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())->get('/configuracoes')->assertOk();
    }

    public function test_admin_can_update_institution_names_without_a_logo(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/configuracoes', [
            'nome_instituicao' => 'Nova Instituicao',
            'nome_secretaria'  => 'Nova Secretaria',
            'unidade_saude'    => 'Nova Unidade',
        ])->assertRedirect(route('configuracoes.edit'));

        $this->assertDatabaseHas('configuracoes', [
            'nome_instituicao' => 'Nova Instituicao',
            'nome_secretaria'  => 'Nova Secretaria',
            'unidade_saude'    => 'Nova Unidade',
        ]);
    }

    public function test_admin_can_upload_a_logo(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/configuracoes', [
            'nome_instituicao' => 'Instituicao',
            'nome_secretaria'  => 'Secretaria',
            'unidade_saude'    => 'Unidade',
            'logo'             => UploadedFile::fake()->image('logo.png', 200, 200),
        ])->assertRedirect(route('configuracoes.edit'));

        $configuracao = Configuracao::atual();
        $this->assertNotNull($configuracao->logo_path);
        // Salvo direto em public/ (não no disco `public` do Storage) porque a
        // hospedagem não tem terminal pra rodar `storage:link`.
        $this->assertFileExists(public_path($configuracao->logo_path));
        @unlink(public_path($configuracao->logo_path));
    }

    public function test_non_admin_cannot_update_configuracoes(): void
    {
        $rt = User::factory()->rt()->create();

        $this->actingAs($rt)->post('/configuracoes', [
            'nome_instituicao' => 'Nao Deveria Salvar',
            'nome_secretaria'  => 'Nao Deveria Salvar',
            'unidade_saude'    => 'Nao Deveria Salvar',
        ])->assertForbidden();

        $this->assertDatabaseMissing('configuracoes', ['nome_instituicao' => 'Nao Deveria Salvar']);
    }

    public function test_update_requires_the_required_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/configuracoes', [])
            ->assertSessionHasErrors(['nome_instituicao', 'nome_secretaria', 'unidade_saude']);
    }
}
