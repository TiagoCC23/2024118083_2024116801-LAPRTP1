<?php

namespace Tests\Feature;

use App\Models\Sala;
use App\Models\Edificio;
use App\Enums\Disponibilidade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_salas()
    {
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        Sala::create([
            'nome_numero' => 'Sala 1',
            'capacidade_maxima' => 20,
            'tipo_sala' => 'SalaDeAula',
            'disponibilidade' => Disponibilidade::Disponivel->value,
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);

        $response = $this->get(route('salas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('salas.index');
    }

    public function test_store_creates_sala()
    {
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);

        $response = $this->post(route('salas.store'), [
            'nome_numero' => 'Sala 2',
            'capacidade_maxima' => 30,
            'tipo_sala' => 'Exame',
            'disponibilidade' => Disponibilidade::Disponivel->value,
            'piso' => 2,
            'edificio_id' => $edificio->id,
        ]);

        $this->assertDatabaseHas('salas', [
            'nome_numero' => 'Sala 2',
            'capacidade_maxima' => 30,
        ]);

        $response->assertRedirect(route('salas.index'));
    }

    public function test_update_modifies_sala()
    {
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        $sala = Sala::create([
            'nome_numero' => 'Sala 1',
            'capacidade_maxima' => 20,
            'tipo_sala' => 'SalaDeAula',
            'disponibilidade' => Disponibilidade::Disponivel->value,
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);

        $response = $this->put(route('salas.update', $sala), [
            'nome_numero' => 'Sala Modificada',
            'capacidade_maxima' => 40,
            'tipo_sala' => 'Trabalho',
            'disponibilidade' => Disponibilidade::Ocupado->value,
            'piso' => 3,
            'edificio_id' => $edificio->id,
        ]);

        $this->assertDatabaseHas('salas', [
            'id' => $sala->id,
            'nome_numero' => 'Sala Modificada',
            'capacidade_maxima' => 40,
        ]);

        $response->assertRedirect(route('salas.index'));
    }

    public function test_destroy_deletes_sala()
    {
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        $sala = Sala::create([
            'nome_numero' => 'Sala 1',
            'capacidade_maxima' => 20,
            'tipo_sala' => 'SalaDeAula',
            'disponibilidade' => Disponibilidade::Disponivel->value,
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);

        $response = $this->delete(route('salas.destroy', $sala));

        $this->assertDatabaseMissing('salas', [
            'id' => $sala->id,
        ]);

        $response->assertRedirect(route('salas.index'));
    }

    public function test_check_disponibilidade()
    {
        $edificio = Edificio::create(['nome' => 'Edificio Teste', 'sigla' => 'ET']);
        $sala = Sala::create([
            'nome_numero' => 'Sala 1',
            'capacidade_maxima' => 20,
            'tipo_sala' => 'SalaDeAula',
            'disponibilidade' => Disponibilidade::Disponivel->value,
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);

        $response = $this->post(action([\App\Http\Controllers\SalaController::class, 'checkDisponibilidade']), [
            'data' => now()->toDateString(),
            'capacidade_maxima' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('salas.disponibilidade');
        $response->assertViewHas('salasDisponiveis');
    }
}
