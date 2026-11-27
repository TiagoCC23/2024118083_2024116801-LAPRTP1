<?php

namespace Database\Seeders;

use App\Enums\EstadoReserva;
use App\Models\Avaliacao;
use App\Models\Reserva;
use Illuminate\Database\Seeder;

class AvaliacaoSeeder extends Seeder
{
    public function run(): void
    {
        $reservaConfirmada = Reserva::where('estado', EstadoReserva::Confirmada->value)
            ->whereDate('data', '<=', now()->toDateString())
            ->orderBy('id')
            ->first();

        if (!$reservaConfirmada) {
            $this->command?->warn('Nenhuma reserva confirmada encontrada para criar avaliação.');
            return;
        }

        Avaliacao::firstOrCreate(
            ['reserva_id' => $reservaConfirmada->id],
            [
                'nota' => 5,
                'comentario' => 'Sala silenciosa e confortável.',
            ]
        );
    }
}
