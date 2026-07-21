<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Rotas restritas a admin + Responsável Técnico (Departamentos,
     * Procedimentos, Colaboradores).
     */
    public static function manageCatalogRoutes(): array
    {
        return [
            'departamentos' => ['/department'],
            'procedimentos' => ['/procedimento'],
            'colaboradores' => ['/rh-users'],
        ];
    }

    /**
     * Rotas restritas só a admin (Configurações, Gestão de Usuários).
     */
    public static function adminOnlyRoutes(): array
    {
        return [
            'configuracoes' => ['/configuracoes'],
            'gestao-de-usuarios' => ['/usuarios'],
        ];
    }

    public function test_guest_is_redirected_to_login_for_protected_routes(): void
    {
        $this->get('/home')->assertRedirect('/login');
        $this->get('/department')->assertRedirect('/login');
    }

    public function test_admin_can_access_manage_catalog_routes(): void
    {
        $admin = User::factory()->admin()->create();

        foreach (self::manageCatalogRoutes() as $route) {
            $this->actingAs($admin)->get($route[0])->assertOk();
        }
    }

    public function test_rt_can_access_manage_catalog_routes(): void
    {
        $rt = User::factory()->rt()->create();

        foreach (self::manageCatalogRoutes() as $route) {
            $this->actingAs($rt)->get($route[0])->assertOk();
        }
    }

    public function test_sec_cannot_access_manage_catalog_routes(): void
    {
        $sec = User::factory()->sec()->create();

        foreach (self::manageCatalogRoutes() as $route) {
            $this->actingAs($sec)->get($route[0])->assertForbidden();
        }
    }

    public function test_rh_cannot_access_manage_catalog_routes(): void
    {
        $rh = User::factory()->rh()->create();

        foreach (self::manageCatalogRoutes() as $route) {
            $this->actingAs($rh)->get($route[0])->assertForbidden();
        }
    }

    public function test_only_admin_can_access_admin_only_routes(): void
    {
        $admin = User::factory()->admin()->create();
        $rt = User::factory()->rt()->create();
        $sec = User::factory()->sec()->create();
        $rh = User::factory()->rh()->create();

        foreach (self::adminOnlyRoutes() as $route) {
            $this->actingAs($admin)->get($route[0])->assertOk();
            $this->actingAs($rt)->get($route[0])->assertForbidden();
            $this->actingAs($sec)->get($route[0])->assertForbidden();
            $this->actingAs($rh)->get($route[0])->assertForbidden();
        }
    }

    public function test_dashboard_and_report_generator_are_reachable_by_every_role(): void
    {
        foreach (['admin', 'rt', 'sec', 'rh'] as $role) {
            $user = User::factory()->{$role}()->create();

            $this->actingAs($user)->get('/home')->assertOk();
            $this->actingAs($user)->get('/gerador')->assertOk();
        }
    }

    public function test_admin_rt_and_sec_see_all_colaboradores_on_dashboard_but_rh_does_not(): void
    {
        User::factory()->rh()->create();

        foreach (['admin', 'rt', 'sec'] as $role) {
            $user = User::factory()->{$role}()->create();
            $this->actingAs($user)->get('/home')->assertViewHas('verTudo', true);
        }

        $rh = User::factory()->rh()->create();
        $this->actingAs($rh)->get('/home')->assertViewHas('verTudo', false);
    }
}
