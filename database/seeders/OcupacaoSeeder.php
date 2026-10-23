<?php

namespace Database\Seeders;

use App\Services\Importador;
use Illuminate\Database\Seeder;

class OcupacaoSeeder extends Seeder
{
    public function run(): void
    {
        app(Importador::class)->importar(database_path('seeders/data'));
    }
}
