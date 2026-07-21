<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_department(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/department/create-department', ['name' => 'Fisioterapia'])
            ->assertRedirect(route('departments'));

        $this->assertDatabaseHas('departments', ['name' => 'Fisioterapia']);
    }

    public function test_cannot_create_a_department_with_a_duplicate_name(): void
    {
        $admin = User::factory()->admin()->create();
        Department::factory()->create(['name' => 'Fisioterapia']);

        $this->actingAs($admin)->post('/department/create-department', ['name' => 'Fisioterapia'])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_a_department(): void
    {
        $admin = User::factory()->admin()->create();
        $department = Department::factory()->create(['name' => 'Nome Antigo']);

        $this->actingAs($admin)->post('/department/update-department', [
            'id'   => $department->id,
            'name' => 'Nome Novo',
        ])->assertRedirect(route('departments'));

        $this->assertDatabaseHas('departments', ['id' => $department->id, 'name' => 'Nome Novo']);
    }

    public function test_deleting_a_department_requires_post_and_unassigns_its_colaboradores(): void
    {
        $admin = User::factory()->admin()->create();
        $department = Department::factory()->create();
        $colaborador = User::factory()->rh()->create(['department_id' => $department->id]);

        $this->actingAs($admin)->get("/department/delete-department-confirm/{$department->id}")->assertMethodNotAllowed();

        $this->actingAs($admin)->post("/department/delete-department-confirm/{$department->id}")
            ->assertRedirect(route('departments'));

        $this->assertSoftDeleted('departments', ['id' => $department->id]);
        $this->assertDatabaseHas('users', ['id' => $colaborador->id, 'department_id' => null]);
    }

    /**
     * Departamentos 1 e 2 são protegidos (usados no fluxo de cadastro de
     * colaborador) e não podem ser editados/excluídos por ninguém.
     */
    public function test_departments_one_and_two_are_protected_from_edit_and_delete(): void
    {
        $admin = User::factory()->admin()->create();

        foreach ([1, 2] as $id) {
            $this->actingAs($admin)->get("/department/edit-department/{$id}")->assertRedirect(route('departments'));
            $this->actingAs($admin)->post("/department/delete-department-confirm/{$id}")->assertRedirect(route('departments'));
        }
    }

    public function test_rt_can_manage_departments_but_sec_and_rh_cannot(): void
    {
        $rt = User::factory()->rt()->create();
        $this->actingAs($rt)->post('/department/create-department', ['name' => 'Acupuntura'])
            ->assertRedirect(route('departments'));

        $sec = User::factory()->sec()->create();
        $this->actingAs($sec)->post('/department/create-department', ['name' => 'Nao Deveria Criar'])
            ->assertForbidden();

        $rh = User::factory()->rh()->create();
        $this->actingAs($rh)->post('/department/create-department', ['name' => 'Nao Deveria Criar 2'])
            ->assertForbidden();
    }
}
