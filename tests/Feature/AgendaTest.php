<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\eventos;
use App\Models\Procediment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class AgendaTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_a_clientes_agenda(): void
    {
        $user = User::factory()->rh()->create();
        $cliente = Client::factory()->create();

        $this->actingAs($user)
            ->get('/agenda/index/' . Crypt::encrypt($cliente->id))
            ->assertOk();
    }

    public function test_can_create_an_evento_with_valid_data(): void
    {
        $user = User::factory()->rh()->create();
        $cliente = Client::factory()->create();
        $procedimento = Procediment::factory()->create();

        $this->actingAs($user)->post('/create-agenda', [
            'title'           => 'Atendimento Teste',
            'start'           => '2026-08-01 08:00:00',
            'end'             => '2026-08-01 09:00:00',
            'client_id'       => $cliente->id,
            'user_id'         => $user->id,
            'procediments_id' => $procedimento->id,
            'color'           => '#FF0000',
            'descricao'       => 'Descrição do atendimento',
        ])->assertRedirect(route('clientes'));

        $this->assertDatabaseHas('eventos', [
            'title'     => 'Atendimento Teste',
            'client_id' => $cliente->id,
            'user_id'   => $user->id,
        ]);
    }

    public function test_cannot_create_an_evento_where_end_is_before_start(): void
    {
        $user = User::factory()->rh()->create();
        $cliente = Client::factory()->create();
        $procedimento = Procediment::factory()->create();

        $this->actingAs($user)->post('/create-agenda', [
            'title'           => 'Atendimento Invalido',
            'start'           => '2026-08-01 09:00:00',
            'end'             => '2026-08-01 08:00:00',
            'client_id'       => $cliente->id,
            'user_id'         => $user->id,
            'procediments_id' => $procedimento->id,
            'color'           => '#FF0000',
            'descricao'       => 'x',
        ])->assertSessionHasErrors('end');

        $this->assertDatabaseMissing('eventos', ['title' => 'Atendimento Invalido']);
    }

    public function test_can_move_an_evento(): void
    {
        $user = User::factory()->rh()->create();
        $evento = eventos::factory()->create([
            'start' => '2026-08-01 08:00:00',
            'end'   => '2026-08-01 09:00:00',
        ]);

        $this->actingAs($user)->post("/agenda/mover/{$evento->id}", [
            'start_date' => '2026-08-02 10:00:00',
            'end_date'   => '2026-08-02 11:00:00',
        ])->assertOk();

        $evento->refresh();
        $this->assertSame('2026-08-02', $evento->start->format('Y-m-d'));
    }

    public function test_deleting_an_evento_requires_post(): void
    {
        $user = User::factory()->rh()->create();
        $evento = eventos::factory()->create();

        $this->actingAs($user)->get("/agenda/delete/{$evento->id}")->assertMethodNotAllowed();

        $this->actingAs($user)->post("/agenda/delete/{$evento->id}")->assertOk();

        $this->assertSoftDeleted('eventos', ['id' => $evento->id]);
    }

    public function test_get_events_only_returns_the_logged_in_therapists_own_events(): void
    {
        $terapeuta = User::factory()->rh()->create();
        $outroTerapeuta = User::factory()->rh()->create();

        eventos::factory()->create(['user_id' => $terapeuta->id, 'status' => '1', 'title' => 'Meu evento']);
        eventos::factory()->create(['user_id' => $outroTerapeuta->id, 'status' => '1', 'title' => 'Evento de outro']);

        $response = $this->actingAs($terapeuta)->get('/events');

        $response->assertOk();
        $titulos = collect($response->json())->pluck('title');
        $this->assertTrue($titulos->contains('Meu evento'));
        $this->assertFalse($titulos->contains('Evento de outro'));
    }
}
