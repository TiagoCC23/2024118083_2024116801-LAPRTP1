<?php

namespace Database\Seeders;

use App\Enums\EstadoReserva;
use App\Models\Avaliacao;
use App\Models\Horario;
use App\Models\Reserva;
use App\Models\Sala;
use App\Models\Utilizador;
use Illuminate\Database\Seeder;

class ReservaSeeder extends Seeder
{
    public function run(): void
    {
        $estudante1 = Utilizador::where('email', 'estudante1@ufp.test')->first();
        $estudante2 = Utilizador::where('email', 'estudante2@ufp.test')->first();

        $admin = Utilizador::where('tipo_user', 'admin')->first();
        $salas = Sala::when($admin?->edificio_id, function ($query) use ($admin) {
            $query->where('edificio_id', $admin->edificio_id);
        })->orderBy('id')->get();

        if ($salas->isEmpty()) {
            $salas = Sala::all();
        }

        $h = Horario::orderBy('hora_inicio')->get();

        if ($salas->isEmpty() || $h->count() < 6 || !$estudante1 || !$estudante2) {
            $this->command->error('Dados insuficientes para rodar o ReservaSeeder! Verifique se Salas, Horarios e Estudantes já foram inseridos.');
            return;
        }

        $r1 = Reserva::create([
            'data' => now()->subDays(3)->toDateString(),
            'quantidade_pessoas' => 1,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'Estudo individual',
            'sala_id' => $salas[0]->id,
            'utilizador_id' => $estudante1->id,
        ]);
        $r1->horarios()->attach([$h[0]->id, $h[1]->id]);

        Avaliacao::create([
            'reserva_id' => $r1->id,
            'nota' => 5,
            'comentario' => 'Sala silenciosa e confortável.'
        ]);

        $sala2 = $salas->get(1) ?? $salas[0];

        $r2 = Reserva::create([
            'data' => now()->addDays(2)->toDateString(),
            'quantidade_pessoas' => 4,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'Trabalho de grupo',
            'sala_id' => $sala2->id,
            'utilizador_id' => $estudante2->id,
        ]);
        $r2->horarios()->attach([$h[3]->id, $h[4]->id, $h[5]->id]);
    }
}
