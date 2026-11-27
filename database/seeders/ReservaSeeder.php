<?php

namespace Database\Seeders;

use App\Enums\EstadoReserva;
use App\Enums\TipoUtilizador;
use App\Models\Horario;
use App\Models\Reserva;
use App\Models\Sala;
use App\Models\Utilizador;
use Illuminate\Database\Seeder;

class ReservaSeeder extends Seeder
{
    public function run(): void
    {
        $estudante1 = Utilizador::where('email', env('SEED_ESTUDANTE1_EMAIL', 'estudante1@ufp.test'))->first();
        $estudante2 = Utilizador::where('email', env('SEED_ESTUDANTE2_EMAIL', 'estudante2@ufp.test'))->first();

        $admin = Utilizador::where('tipo_user', TipoUtilizador::Admin->value)->first();
        $salas = Sala::when($admin?->edificio_id, function ($query) use ($admin) {
            $query->where('edificio_id', $admin->edificio_id);
        })->orderBy('id')->get();

        if ($salas->isEmpty()) {
            $salas = Sala::orderBy('id')->get();
        }

        $horarios = Horario::orderBy('hora_inicio')->get();

        if ($salas->isEmpty() || $horarios->count() < 6 || !$estudante1 || !$estudante2) {
            $this->command?->error('Dados insuficientes para rodar o ReservaSeeder! Verifique se Salas, Horarios e Estudantes já foram inseridos.');
            return;
        }

        $dataPassada = now()->subDays(3);
        if ($dataPassada->isSunday()) {
            $dataPassada = $dataPassada->subDay();
        }

        $dataFutura = now()->addDays(2);
        if ($dataFutura->isSunday()) {
            $dataFutura = $dataFutura->addDay();
        }

        $r1 = Reserva::whereDate('data', $dataPassada->toDateString())
            ->where('sala_id', $salas[0]->id)
            ->where('utilizador_id', $estudante1->id)
            ->first()
            ?? Reserva::create([
                'data' => $dataPassada->toDateString(),
                'sala_id' => $salas[0]->id,
                'utilizador_id' => $estudante1->id,
                'quantidade_pessoas' => 1,
                'estado' => EstadoReserva::Confirmada,
                'tipo_de_uso' => 'Estudo',
            ]);
        $r1->horarios()->sync([$horarios[0]->id, $horarios[1]->id]);

        $sala2 = $salas->get(1) ?? $salas[0];

        $r2 = Reserva::whereDate('data', $dataFutura->toDateString())
            ->where('sala_id', $sala2->id)
            ->where('utilizador_id', $estudante2->id)
            ->first()
            ?? Reserva::create([
                'data' => $dataFutura->toDateString(),
                'sala_id' => $sala2->id,
                'utilizador_id' => $estudante2->id,
                'quantidade_pessoas' => min(4, $sala2->capacidade_maxima),
                'estado' => EstadoReserva::Pendente,
                'tipo_de_uso' => 'AulaPratica',
            ]);
        $r2->horarios()->sync([$horarios[3]->id, $horarios[4]->id, $horarios[5]->id]);
    }
}
