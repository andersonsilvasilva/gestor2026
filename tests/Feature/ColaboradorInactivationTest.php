<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColaboradorInactivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_manage_catalog_roles_can_toggle_a_colaborador_status(): void
    {
        $colaborador = User::factory()->rh()->create();

        $sec = User::factory()->sec()->create();
        $this->actingAs($sec)->post("/rh-users/toggle-status/{$colaborador->id}")->assertForbidden();
        $this->assertTrue($colaborador->refresh()->ativo);

        $rt = User::factory()->rt()->create();
        $this->actingAs($rt)->post("/rh-users/toggle-status/{$colaborador->id}")->assertRedirect();
        $this->assertFalse($colaborador->refresh()->ativo);
    }

    public function test_manage_catalog_user_cannot_inactivate_themselves(): void
    {
        $rt = User::factory()->rt()->create();

        $this->actingAs($rt)->post("/rh-users/toggle-status/{$rt->id}")->assertRedirect();

        $this->assertTrue($rt->refresh()->ativo);
    }

    public function test_inactive_colaborador_is_excluded_from_dashboard_and_report_generator_selectors(): void
    {
        $admin = User::factory()->admin()->create();
        $ativo = User::factory()->rh()->create(['name' => 'Colaborador Ativo']);
        $inativo = User::factory()->rh()->inativo()->create(['name' => 'Colaborador Inativo']);

        $dashboard = $this->actingAs($admin)->get('/home');
        $dashboard->assertOk();
        $this->assertTrue($dashboard->viewData('colaboradores')->contains('id', $ativo->id));
        $this->assertFalse($dashboard->viewData('colaboradores')->contains('id', $inativo->id));

        $gerador = $this->actingAs($admin)->get('/gerador');
        $gerador->assertOk();
        $this->assertTrue($gerador->viewData('terapeutas')->contains('id', $ativo->id));
        $this->assertFalse($gerador->viewData('terapeutas')->contains('id', $inativo->id));
    }
}
