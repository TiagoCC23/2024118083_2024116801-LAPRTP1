<?php

namespace Tests\Unit;

use App\Enums\EstadoReserva;
use App\Enums\TipoUtilizador;
use App\Models\Avaliacao;
use App\Models\Edificio;
use App\Models\Reserva;
use App\Models\Sala;
use App\Models\Utilizador;
use App\Policies\ReservaPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservaPolicyTest extends TestCase
{
    use RefreshDatabase;

    private ReservaPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ReservaPolicy();
    }

    private function criarCenario(): array
    {
        $edificio = Edificio::create(['nome' => 'Edifício UFP', 'sigla' => 'UFP']);
        $sala = Sala::create([
            'nome_numero' => 'Sala 101',
            'capacidade_maxima' => 30,
            'tipo_sala' => 'Trabalho',
            'piso' => 1,
            'disponibilidade' => 'disponivel',
            'edificio_id' => $edificio->id,
        ]);
        $outraSala = Sala::create([
            'nome_numero' => 'Sala 102',
            'capacidade_maxima' => 20,
            'tipo_sala' => 'Trabalho',
            'piso' => 1,
            'disponibilidade' => 'disponivel',
            'edificio_id' => $edificio->id,
        ]);

        $dono = Utilizador::create([
            'nome' => 'Dono',
            'email' => 'dono@ufp.test',
            'password_hash' => 'hash',
            'tipo_user' => TipoUtilizador::Estudante,
        ]);
        $outroEstudante = Utilizador::create([
            'nome' => 'Outro',
            'email' => 'outro@ufp.test',
            'password_hash' => 'hash',
            'tipo_user' => TipoUtilizador::Estudante,
        ]);
        $adminDaSala = Utilizador::create([
            'nome' => 'Admin Sala',
            'email' => 'admin.sala@ufp.test',
            'password_hash' => 'hash',
            'tipo_user' => TipoUtilizador::Admin,
        ]);
        $outroAdmin = Utilizador::create([
            'nome' => 'Outro Admin',
            'email' => 'admin.outro@ufp.test',
            'password_hash' => 'hash',
            'tipo_user' => TipoUtilizador::Admin,
        ]);

        $adminDaSala->salasAdministradas()->attach($sala->id);
        $outroAdmin->salasAdministradas()->attach($outraSala->id);

        return compact('sala', 'outraSala', 'dono', 'outroEstudante', 'adminDaSala', 'outroAdmin');
    }

    public function test_view_permite_dono_e_admin_da_sala_e_nega_restantes(): void
    {
        $c = $this->criarCenario();
        $reserva = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $c['sala']->id,
            'utilizador_id' => $c['dono']->id,
        ]);

        $this->assertTrue($this->policy->view($c['dono'], $reserva));
        $this->assertTrue($this->policy->view($c['adminDaSala'], $reserva));
        $this->assertFalse($this->policy->view($c['outroEstudante'], $reserva));
        $this->assertFalse($this->policy->view($c['outroAdmin'], $reserva));
    }

    public function test_update_e_delete_permitem_dono_pendente_ou_admin_da_sala(): void
    {
        $c = $this->criarCenario();

        $pendente = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $c['sala']->id,
            'utilizador_id' => $c['dono']->id,
        ]);

        $confirmada = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $c['sala']->id,
            'utilizador_id' => $c['dono']->id,
        ]);

        // Dono
        $this->assertTrue($this->policy->update($c['dono'], $pendente));
        $this->assertTrue($this->policy->delete($c['dono'], $pendente));
        $this->assertFalse($this->policy->update($c['dono'], $confirmada));
        $this->assertFalse($this->policy->delete($c['dono'], $confirmada));

        // Admin da sala
        $this->assertTrue($this->policy->update($c['adminDaSala'], $pendente));
        $this->assertTrue($this->policy->update($c['adminDaSala'], $confirmada));
        $this->assertTrue($this->policy->delete($c['adminDaSala'], $confirmada));

        // Terceiros / admin de outra sala
        $this->assertFalse($this->policy->update($c['outroEstudante'], $pendente));
        $this->assertFalse($this->policy->update($c['outroAdmin'], $pendente));
        $this->assertFalse($this->policy->delete($c['outroAdmin'], $pendente));
    }

    public function test_confirmar_requer_estado_pendente_e_admin_da_sala(): void
    {
        $c = $this->criarCenario();

        $pendente = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $c['sala']->id,
            'utilizador_id' => $c['dono']->id,
        ]);

        $confirmada = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $c['sala']->id,
            'utilizador_id' => $c['dono']->id,
        ]);

        $this->assertTrue($this->policy->confirmar($c['adminDaSala'], $pendente));
        $this->assertFalse($this->policy->confirmar($c['adminDaSala'], $confirmada));
        $this->assertFalse($this->policy->confirmar($c['dono'], $pendente));
        $this->assertFalse($this->policy->confirmar($c['outroAdmin'], $pendente));
    }

    public function test_cancelar_bloqueia_quando_ja_cancelada_ou_sem_permissao(): void
    {
        $c = $this->criarCenario();

        $confirmada = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $c['sala']->id,
            'utilizador_id' => $c['dono']->id,
        ]);

        $cancelada = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Cancelada,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $c['sala']->id,
            'utilizador_id' => $c['dono']->id,
        ]);

        $this->assertTrue($this->policy->cancelar($c['dono'], $confirmada));
        $this->assertTrue($this->policy->cancelar($c['adminDaSala'], $confirmada));
        $this->assertFalse($this->policy->cancelar($c['outroEstudante'], $confirmada));
        $this->assertFalse($this->policy->cancelar($c['outroAdmin'], $confirmada));
        $this->assertFalse($this->policy->cancelar($c['dono'], $cancelada));
        $this->assertFalse($this->policy->cancelar($c['adminDaSala'], $cancelada));
    }

    public function test_avaliar_valida_dono_estado_data_e_unicidade(): void
    {
        $c = $this->criarCenario();

        $validaPassada = Reserva::create([
            'data' => now()->subDays(2)->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $c['sala']->id,
            'utilizador_id' => $c['dono']->id,
        ]);

        $validaHoje = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $c['sala']->id,
            'utilizador_id' => $c['dono']->id,
        ]);

        $futura = Reserva::create([
            'data' => now()->addDays(2)->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $c['sala']->id,
            'utilizador_id' => $c['dono']->id,
        ]);

        $pendente = Reserva::create([
            'data' => now()->subDay()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $c['sala']->id,
            'utilizador_id' => $c['dono']->id,
        ]);

        $this->assertTrue($this->policy->avaliar($c['dono'], $validaPassada));
        $this->assertTrue($this->policy->avaliar($c['dono'], $validaHoje));
        $this->assertFalse($this->policy->avaliar($c['outroEstudante'], $validaPassada));
        $this->assertFalse($this->policy->avaliar($c['dono'], $futura));
        $this->assertFalse($this->policy->avaliar($c['dono'], $pendente));

        Avaliacao::create(['nota' => 5, 'comentario' => 'Bom', 'reserva_id' => $validaPassada->id]);
        $this->assertFalse($this->policy->avaliar($c['dono'], $validaPassada));
    }

    public function test_alterar_estado_regras_para_admin_e_estudante(): void
    {
        $c = $this->criarCenario();

        $pendente = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $c['sala']->id,
            'utilizador_id' => $c['dono']->id,
        ]);

        $confirmada = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'SalaDeEstudo',
            'sala_id' => $c['sala']->id,
            'utilizador_id' => $c['dono']->id,
        ]);

        // Admin pode aprovar, rejeitar ou cancelar, mas não voltar para pendente
        $this->assertTrue($this->policy->alterarEstado($c['adminDaSala'], $pendente, EstadoReserva::Confirmada));
        $this->assertTrue($this->policy->alterarEstado($c['adminDaSala'], $pendente, EstadoReserva::Rejeitada));
        $this->assertTrue($this->policy->alterarEstado($c['adminDaSala'], $confirmada, EstadoReserva::Cancelada));
        $this->assertFalse($this->policy->alterarEstado($c['adminDaSala'], $confirmada, EstadoReserva::Pendente));

        // Dono só pode cancelar se a reserva ainda estiver pendente
        $this->assertTrue($this->policy->alterarEstado($c['dono'], $pendente, EstadoReserva::Cancelada));
        $this->assertFalse($this->policy->alterarEstado($c['dono'], $pendente, EstadoReserva::Confirmada));
        $this->assertFalse($this->policy->alterarEstado($c['dono'], $pendente, EstadoReserva::Rejeitada));
        $this->assertFalse($this->policy->alterarEstado($c['dono'], $confirmada, EstadoReserva::Cancelada));

        // Outro estudante nunca pode alterar
        $this->assertFalse($this->policy->alterarEstado($c['outroEstudante'], $pendente, EstadoReserva::Cancelada));
    }
}
