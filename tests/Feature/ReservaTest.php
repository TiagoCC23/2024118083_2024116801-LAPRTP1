<?php

namespace Tests\Feature;

use App\Enums\EstadoReserva;
use App\Models\Avaliacao;
use App\Models\Edificio;
use App\Models\Horario;
use App\Models\Reserva;
use App\Models\Sala;
use App\Models\Utilizador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservaTest extends TestCase
{
    use RefreshDatabase;

    public function test_reserva_possui_relacionamentos_corretos(): void
    {
        $edificio = Edificio::create(['nome' => 'Bloco A', 'sigla' => 'BLA']);

        $sala = Sala::create([
            'nome_numero' => 'Sala 101',
            'capacidade_maxima' => 30,
            'tipo_sala' => 'Aula',
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);

        $utilizador = Utilizador::create([
            'nome' => 'João Silva',
            'email' => 'joao@exemplo.com',
            'password_hash' => 'password123',
            'tipo_user' => 'estudante',
            'edificio_id' => $edificio->id,
        ]);

        $reserva = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 20,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'Aula Prática',
            'sala_id' => $sala->id,
            'utilizador_id' => $utilizador->id,
        ]);

        $horario = Horario::create([
            'hora_inicio' => '10:00',
            'hora_fim' => '12:00',
        ]);

        $reserva->horarios()->attach($horario->id);

        $avaliacao = Avaliacao::create([
            'reserva_id' => $reserva->id,
            'nota' => 5,
            'comentario' => 'Espaço muito bom!',
        ]);

        // Asserções
        $this->assertEquals($sala->id, $reserva->sala->id);
        $this->assertEquals($utilizador->id, $reserva->utilizador->id);
        $this->assertCount(1, $reserva->horarios);
        $this->assertEquals('10:00 - 12:00', $reserva->horarios->first()->intervalo);
        $this->assertEquals(5, $reserva->avaliacao->nota);
        $this->assertInstanceOf(EstadoReserva::class, $reserva->estado);
    }
}
