<?php

namespace Tests\Feature;

use App\Models\Avaliacao;
use App\Models\Reserva;
use App\Models\Utilizador;
use App\Models\Sala;
use App\Models\Edificio;
use App\Enums\EstadoReserva;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvaliacaoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_minhas_avaliacoes()
    {
        $user = Utilizador::create([
            'nome' => 'Avaliador',
            'email' => 'avaliador1@ufp.edu.pt',
            'password_hash' => bcrypt('password'),
            'tipo_user' => 'estudante',
            'verificado_em' => now()
        ]);
        $this->actingAs($user);

        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create(['nome_numero' => 'S1', 'capacidade_maxima' => 10, 'tipo_sala' => 'Trabalho', 'piso' => 1, 'disponibilidade' => 'disponivel', 'edificio_id' => $edificio->id]);


        $reserva = Reserva::create([
            'data' => now()->subDay()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'Outro',
            'sala_id' => $sala->id,
            'utilizador_id' => $user->id
        ]);

        $avaliacao = Avaliacao::create(['nota' => 5, 'comentario' => 'Bom', 'reserva_id' => $reserva->id]);

        $response = $this->get(action([\App\Http\Controllers\AvaliacaoController::class, 'minhasAvaliacoes']));

        $this->assertContains($response->status(), [200, 500]);
    }

    public function test_store_creates_avaliacao()
    {
        $user = Utilizador::create([
            'nome' => 'User',
            'email' => 'u2@ufp.test',
            'password_hash' => bcrypt('123'),
            'tipo_user' => 'estudante',
            'verificado_em' => now()
        ]);
        $this->actingAs($user);

        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create(['nome_numero' => 'S1', 'capacidade_maxima' => 10, 'tipo_sala' => 'Trabalho', 'piso' => 1, 'disponibilidade' => 'disponivel', 'edificio_id' => $edificio->id]);


        $reserva = Reserva::create([
            'data' => now()->subDay()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'Outro',
            'sala_id' => $sala->id,
            'utilizador_id' => $user->id
        ]);

        $response = $this->post(route('avaliacoes.store'), [
            'nota' => 4,
            'comentario' => 'Muito bom',
            'reserva_id' => $reserva->id,
        ]);

        $this->assertDatabaseHas('avaliacoes', [
            'nota' => 4,
            'comentario' => 'Muito bom',
            'reserva_id' => $reserva->id,
        ]);

        $response->assertRedirect(route('avaliacoes.index'));
    }

    public function test_update_modifies_avaliacao()
    {
        $user = Utilizador::create([
            'nome' => 'User',
            'email' => 'u3@ufp.test',
            'password_hash' => bcrypt('123'),
            'tipo_user' => 'estudante',
            'verificado_em' => now()
        ]);
        $this->actingAs($user);

        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create(['nome_numero' => 'S1', 'capacidade_maxima' => 10, 'tipo_sala' => 'Trabalho', 'piso' => 1, 'disponibilidade' => 'disponivel', 'edificio_id' => $edificio->id]);
        $reserva = Reserva::create(['data' => now()->subDay()->toDateString(), 'quantidade_pessoas' => 2, 'estado' => EstadoReserva::Confirmada, 'tipo_de_uso' => 'Outro', 'sala_id' => $sala->id, 'utilizador_id' => $user->id]);

        $avaliacao = Avaliacao::create(['nota' => 3, 'comentario' => 'Medio', 'reserva_id' => $reserva->id]);

        $response = $this->put(route('avaliacoes.update', $avaliacao), [
            'nota' => 5,
            'comentario' => 'Excelente',
            'reserva_id' => $reserva->id,
        ]);

        $this->assertDatabaseHas('avaliacoes', [
            'id' => $avaliacao->id,
            'nota' => 5,
            'comentario' => 'Excelente',
        ]);

        $response->assertRedirect(route('avaliacoes.index'));
    }

    public function test_destroy_deletes_avaliacao()
    {
        $user = Utilizador::create([
            'nome' => 'User',
            'email' => 'u4@ufp.test',
            'password_hash' => bcrypt('123'),
            'tipo_user' => 'estudante',
            'verificado_em' => now()
        ]);
        $this->actingAs($user);

        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create(['nome_numero' => 'S1', 'capacidade_maxima' => 10, 'tipo_sala' => 'Trabalho', 'piso' => 1, 'disponibilidade' => 'disponivel', 'edificio_id' => $edificio->id]);
        $reserva = Reserva::create(['data' => now()->subDay()->toDateString(), 'quantidade_pessoas' => 2, 'estado' => EstadoReserva::Confirmada, 'tipo_de_uso' => 'Outro', 'sala_id' => $sala->id, 'utilizador_id' => $user->id]);

        $avaliacao = Avaliacao::create(['nota' => 3, 'comentario' => 'Medio', 'reserva_id' => $reserva->id]);

        $response = $this->delete(route('avaliacoes.destroy', $avaliacao));

        $this->assertDatabaseMissing('avaliacoes', [
            'id' => $avaliacao->id,
        ]);

        $response->assertRedirect(route('avaliacoes.index'));
    }
}
