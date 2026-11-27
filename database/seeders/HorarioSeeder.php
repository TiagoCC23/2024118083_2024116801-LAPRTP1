<?php

namespace Database\Seeders;

use App\Models\Horario;
use Illuminate\Database\Seeder;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        for ($h = 8; $h < 18; $h++) {
            Horario::firstOrCreate([
                'hora_inicio' => sprintf('%02d:00:00', $h),
                'hora_fim' => sprintf('%02d:00:00', $h + 1),
            ]);
        }
    }
}
