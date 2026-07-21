<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_create_a_user(): void
    {
        Mail::fake();

        $rt = User::factory()->rt()->create();
        $this->actingAs($rt)->post('/usuarios/criar', [
            'name' => 'Novo RT',
            'email' => 'novo-rt@teste.com',
            'role' => 'rt',
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'novo-rt@teste.com']);
    }

    public function test_admin_can_create_a_user_with_a_valid_role(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post('/usuarios/criar', [
            'name' => 'Secretaria Nova',
            'email' => 'secretaria-nova@teste.com',
            'role' => 'sec',
        ])->assertRedirect(route('usuarios.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'secretaria-nova@teste.com',
            'role' => 'sec',
            'ativo' => 1,
        ]);
    }

    public function test_cannot_create_a_user_with_the_rh_role(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->post('/usuarios/criar', [
            'name' => 'Tentando criar rh',
            'email' => 'tentando-rh@teste.com',
            'role' => 'rh',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'tentando-rh@teste.com']);
    }

    public function test_rh_colaboradores_do_not_appear_in_user_management_list(): void
    {
        $admin = User::factory()->admin()->create();
        $rh = User::factory()->rh()->create(['name' => 'Colaborador RH Teste']);

        $this->actingAs($admin)->get('/usuarios')->assertOk()->assertDontSee('Colaborador RH Teste');
    }

    public function test_admin_cannot_inactivate_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post("/usuarios/toggle-status/{$admin->id}")
            ->assertRedirect(route('usuarios.index'));

        $this->assertTrue($admin->refresh()->ativo);
    }

    public function test_admin_can_toggle_another_users_status(): void
    {
        $admin = User::factory()->admin()->create();
        $rt = User::factory()->rt()->create();

        $this->actingAs($admin)->post("/usuarios/toggle-status/{$rt->id}");
        $this->assertFalse($rt->refresh()->ativo);

        $this->actingAs($admin)->post("/usuarios/toggle-status/{$rt->id}");
        $this->assertTrue($rt->refresh()->ativo);
    }
}
