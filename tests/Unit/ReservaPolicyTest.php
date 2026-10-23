<?php

namespace Tests\Unit;

use App\Enums\EstadoReserva;
use App\Models\Avaliacao;
use App\Models\Reserva;
use App\Models\Utilizador;
use App\Policies\ReservaPolicy;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Mockery;
use Tests\TestCase;

class ReservaPolicyTest extends TestCase
{
    private ReservaPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ReservaPolicy();
    }

    public function test_dono_pode_visualizar_reserva(): void
    {
        $user = new Utilizador();
        $user->id = 1;
        $user->tipo_user = 'comum';

        $reserva = new Reserva();
        $reserva->utilizador_id = 1;

        $this->assertTrue($this->policy->view($user, $reserva));
    }

    public function test_utilizador_diferente_sem_ser_admin_nao_pode_visualizar_reserva(): void
    {
        $user = Mockery::mock(Utilizador::class)->makePartial();
        $user->id = 2;
        $user->tipo_user = 'comum';

        $reserva = new Reserva();
        $reserva->utilizador_id = 1;

        $this->assertFalse($this->policy->view($user, $reserva));
    }

    public function test_dono_pode_atualizar_apenas_se_estiver_pendente(): void
    {
        $user = new Utilizador();
        $user->id = 1;
        $user->tipo_user = 'comum';

        $reservaPendente = new Reserva(['estado' => EstadoReserva::Pendente]);
        $reservaPendente->utilizador_id = 1;

        $reservaConfirmada = new Reserva(['estado' => EstadoReserva::Confirmada]);
        $reservaConfirmada->utilizador_id = 1;

        $this->assertTrue($this->policy->update($user, $reservaPendente));
        $this->assertFalse($this->policy->update($user, $reservaConfirmada));
    }

    public function test_cancelar_reserva_bloqueado_quando_ja_estiver_cancelada(): void
    {
        $user = new Utilizador();
        $user->id = 1;

        $reservaCancelada = new Reserva(['estado' => EstadoReserva::Cancelada]);
        $reservaCancelada->utilizador_id = 1;

        $this->assertFalse($this->policy->cancelar($user, $reservaCancelada));
    }

    public function test_avaliar_reserva_requer_confirmada_data_passada_e_sem_avaliacao(): void
    {
        $user = new Utilizador();
        $user->id = 1;

        $reservaValida = Mockery::mock(Reserva::class)->makePartial();
        $reservaValida->utilizador_id = 1;
        $reservaValida->estado = EstadoReserva::Confirmada;
        $reservaValida->data = now()->subDays(2)->startOfDay();

        $hasOneMock = Mockery::mock(HasOne::class);
        $hasOneMock->shouldReceive('exists')->andReturn(false);
        $reservaValida->shouldReceive('avaliacao')->andReturn($hasOneMock);

        $this->assertTrue($this->policy->avaliar($user, $reservaValida));
    }

    public function test_avaliar_reserva_falha_se_ja_existir_avaliacao(): void
    {
        $user = new Utilizador();
        $user->id = 1;

        $reserva = Mockery::mock(Reserva::class)->makePartial();
        $reserva->utilizador_id = 1;
        $reserva->estado = EstadoReserva::Confirmada;
        $reserva->data = now()->subDays(1)->startOfDay();

        $hasOneMock = Mockery::mock(HasOne::class);
        $hasOneMock->shouldReceive('exists')->andReturn(true);
        $reserva->shouldReceive('avaliacao')->andReturn($hasOneMock);

        $this->assertFalse($this->policy->avaliar($user, $reserva));
    }
}
