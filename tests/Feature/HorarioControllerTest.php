<?php

namespace Tests\Feature;

use App\Models\Horario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HorarioControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_horarios()
    {
        Horario::create(['hora_inicio' => '08:00:00', 'hora_fim' => '09:00:00']);

        $response = $this->get(route('horarios.index'));

        $response->assertStatus(200);
        $response->assertViewIs('horarios.index');
    }

    public function test_store_creates_horario()
    {
        $response = $this->post(route('horarios.store'), [
            'hora_inicio' => '10:00',
            'hora_fim' => '11:00',
        ]);

        $this->assertDatabaseHas('horarios', [
            'hora_inicio' => '10:00:00',
            'hora_fim' => '11:00:00',
        ]);

        $response->assertRedirect(route('horarios.index'));
    }

    public function test_update_modifies_horario()
    {
        $horario = Horario::create(['hora_inicio' => '08:00:00', 'hora_fim' => '09:00:00']);

        $response = $this->put(route('horarios.update', $horario), [
            'hora_inicio' => '09:00',
            'hora_fim' => '10:00',
        ]);

        $this->assertDatabaseHas('horarios', [
            'id' => $horario->id,
            'hora_inicio' => '09:00:00',
            'hora_fim' => '10:00:00',
        ]);

        $response->assertRedirect(route('horarios.index'));
    }

    public function test_destroy_deletes_horario()
    {
        $horario = Horario::create(['hora_inicio' => '08:00:00', 'hora_fim' => '09:00:00']);

        $response = $this->delete(route('horarios.destroy', $horario));

        $this->assertDatabaseMissing('horarios', [
            'id' => $horario->id,
        ]);

        $response->assertRedirect(route('horarios.index'));
    }
}
