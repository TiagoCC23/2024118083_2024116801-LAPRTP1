<?php

namespace Database\Seeders;

use App\Models\Horario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HorarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($h = 8; $h < 18; $h++) { // -> blocos de 1h
            Horario::create([
                'hora_inicio' => sprintf('%02d:00', $h),
                'hora_fim' => sprintf('%02d:00', $h + 1),
            ]);
        }
    }
}
