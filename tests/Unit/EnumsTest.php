<?php

namespace Tests\Unit;

use App\Enums\Disponibilidade;
use App\Enums\EstadoReserva;
use App\Enums\TipoUtilizador;
use PHPUnit\Framework\TestCase;

class EnumsTest extends TestCase
{
    public function test_disponibilidade_values_and_labels(): void
    {
        $this->assertSame('disponivel', Disponibilidade::Disponivel->value);
        $this->assertSame('Disponivel', Disponibilidade::Disponivel->label());

        $this->assertSame('pendente', Disponibilidade::Pendente->value);
        $this->assertSame('Pendente', Disponibilidade::Pendente->label());

        $this->assertSame('ocupado', Disponibilidade::Ocupado->value);
        $this->assertSame('Ocupado', Disponibilidade::Ocupado->label());
    }

    public function test_estado_reserva_values_and_labels(): void
    {
        $this->assertSame('pendente', EstadoReserva::Pendente->value);
        $this->assertSame('Pendente', EstadoReserva::Pendente->label());

        $this->assertSame('confirmada', EstadoReserva::Confirmada->value);
        $this->assertSame('Confirmada', EstadoReserva::Confirmada->label());

        $this->assertSame('rejeitada', EstadoReserva::Rejeitada->value);
        $this->assertSame('Rejeitada', EstadoReserva::Rejeitada->label());

        $this->assertSame('cancelada', EstadoReserva::Cancelada->value);
        $this->assertSame('Cancelada', EstadoReserva::Cancelada->label());
    }

    public function test_tipo_utilizador_values_and_labels(): void
    {
        $this->assertSame('estudante', TipoUtilizador::Estudante->value);
        $this->assertSame('Estudante', TipoUtilizador::Estudante->label());

        $this->assertSame('admin', TipoUtilizador::Admin->value);
        $this->assertSame('Administrador', TipoUtilizador::Admin->label());
    }
}
