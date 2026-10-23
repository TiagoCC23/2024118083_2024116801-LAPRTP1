<?php

namespace Database\Seeders;

use App\Enums\TipoSala;
use App\Models\Avaliacao;
use App\Models\Horario;
use App\Models\Reserva;
use App\Models\Sala;
use App\Models\Utilizador;
use App\Enums\EstadoReserva;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalaSeeder extends Seeder
{
    protected $casts = ['tipo_sala' => TipoSala::class];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
    }
}
