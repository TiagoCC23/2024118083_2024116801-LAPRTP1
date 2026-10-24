<?php

namespace Tests\Unit;

use App\Models\Avaliacao;
use App\Models\Edificio;
use App\Models\Horario;
use App\Models\Ocupacao;
use App\Models\Reserva;
use App\Models\Sala;
use App\Models\Utilizador;
use App\Enums\EstadoReserva;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_edificio_has_many_salas()
    {
        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create(['nome_numero' => 'Sala 1', 'capacidade_maxima' => 20, 'tipo_sala' => 'SalaDeAula', 'disponibilidade' => 'disponivel', 'piso' => 1, 'edificio_id' => $edificio->id]);

        $this->assertTrue($edificio->salas->contains($sala));
        $this->assertInstanceOf(Edificio::class, $sala->edificio);
    }

    public function test_reserva_belongs_to_sala_and_utilizador()
    {
        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create(['nome_numero' => 'Sala 1', 'capacidade_maxima' => 20, 'tipo_sala' => 'SalaDeAula', 'disponibilidade' => 'disponivel', 'piso' => 1, 'edificio_id' => $edificio->id]);
        $user = Utilizador::create(['nome' => 'Test', 'email' => 'test@ufp.pt', 'password_hash' => 'hash', 'tipo_user' => 'estudante']);
        $reserva = Reserva::create(['data' => now()->toDateString(), 'quantidade_pessoas' => 1, 'estado' => EstadoReserva::Pendente, 'tipo_de_uso' => 'Outro', 'sala_id' => $sala->id, 'utilizador_id' => $user->id]);

        $this->assertInstanceOf(Sala::class, $reserva->sala);
        $this->assertInstanceOf(Utilizador::class, $reserva->utilizador);
    }

    public function test_avaliacao_belongs_to_reserva()
    {
        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create(['nome_numero' => 'Sala 1', 'capacidade_maxima' => 20, 'tipo_sala' => 'SalaDeAula', 'disponibilidade' => 'disponivel', 'piso' => 1, 'edificio_id' => $edificio->id]);
        $user = Utilizador::create(['nome' => 'Test', 'email' => 'test@ufp.pt', 'password_hash' => 'hash', 'tipo_user' => 'estudante']);
        $reserva = Reserva::create(['data' => now()->toDateString(), 'quantidade_pessoas' => 1, 'estado' => EstadoReserva::Pendente, 'tipo_de_uso' => 'Outro', 'sala_id' => $sala->id, 'utilizador_id' => $user->id]);

        $avaliacao = Avaliacao::create(['nota' => 5, 'comentario' => 'Bom', 'reserva_id' => $reserva->id]);

        $this->assertInstanceOf(Reserva::class, $avaliacao->reserva);
    }

    public function test_horario_intervalo_attribute()
    {
        $horario = Horario::create(['hora_inicio' => '08:00:00', 'hora_fim' => '09:00:00']);
        $this->assertEquals('08:00 - 09:00', $horario->intervalo);
    }

    public function test_utilizador_is_admin()
    {
        $admin = Utilizador::create(['nome' => 'Admin', 'email' => 'admin@ufp.pt', 'password_hash' => 'hash', 'tipo_user' => 'admin']);
        $estudante = Utilizador::create(['nome' => 'Estudante', 'email' => 'estudante@ufp.pt', 'password_hash' => 'hash', 'tipo_user' => 'estudante']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($estudante->isAdmin());
    }

    public function test_ocupacao_belongs_to_sala()
    {
        $edificio = Edificio::create(['nome' => 'Edificio', 'sigla' => 'ED']);
        $sala = Sala::create(['nome_numero' => 'Sala 1', 'capacidade_maxima' => 20, 'tipo_sala' => 'SalaDeAula', 'disponibilidade' => 'disponivel', 'piso' => 1, 'edificio_id' => $edificio->id]);

        $ocupacao = Ocupacao::create([
            'data_inicio' => now(),
            'data_fim' => now()->addHour(),
            'tipo_ocupacao' => 'Aula',
            'estado' => 'Confirmada',
            'sala_id' => $sala->id
        ]);

        $this->assertInstanceOf(Sala::class, $ocupacao->sala);
    }
}
