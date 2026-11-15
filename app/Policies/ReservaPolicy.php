<?php

namespace App\Policies;

use App\Enums\EstadoReserva;
use App\Models\Reserva;
use App\Models\Utilizador;

class ReservaPolicy
{
    private function dono(Utilizador $u, Reserva $r): bool
    {
        return $r->utilizador_id === $u->id;
    }

    private function adminDaSala(Utilizador $u, Reserva $r): bool
    {
        return $u->isAdmin() && $u->salasAdministradas()->whereKey($r->sala_id)->exists();
    }

    public function view(Utilizador $u, Reserva $r): bool
    {
        return $this->dono($u, $r) || $this->adminDaSala($u, $r);
    }

    public function update(Utilizador $u, Reserva $r): bool
    {
        return ($this->dono($u, $r) && $r->estado === EstadoReserva::Pendente) || $this->adminDaSala($u, $r);
    }

    public function delete(Utilizador $u, Reserva $r): bool
    {
        return $this->update($u, $r);
    }

    public function confirmar(Utilizador $u, Reserva $r): bool
    {
        return $r->estado === EstadoReserva::Pendente && $this->adminDaSala($u, $r);
    }

    public function cancelar(Utilizador $u, Reserva $r): bool
    {
        return $r->estado !== EstadoReserva::Cancelada && ($this->dono($u, $r) || $this->adminDaSala($u, $r));
    }

    public function avaliar(Utilizador $u, Reserva $r): bool
    {
        return $this->dono($u, $r)
            && $r->estado === EstadoReserva::Confirmada
            && $r->data->lte(now()->startOfDay())
            && !$r->avaliacao()->exists();
    }

    public function alterarEstado(Utilizador $u, Reserva $r, EstadoReserva $novoEstado): bool
    {
        /* O admin pode alterar o estado da reserva á vontade (aprova ou rejeita) */
        if($u->isAdmin()){
            return in_array($novoEstado,
                [EstadoReserva::Confirmada,
                 EstadoReserva::Rejeitada,
                 EstadoReserva::Cancelada],
                true);
        }
        /* O user só pode cancelar a sua reserva caso esta esteja pendente*/
        return $u->id === $r->utilizador_id && $r->estado === EstadoReserva::Pendente && $novoEstado === EstadoReserva::Cancelada;

    }

}
