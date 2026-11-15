<?php

namespace Database\Seeders;

use App\Enums\TipoUtilizador;
use App\Models\Edificio;
use App\Models\Utilizador;
use Illuminate\Database\Seeder;

class UtilizadorSeeder extends Seeder
{
    public function run(): void
    {
        Utilizador::create([
            'nome' => 'Admin Biblioteca',
            'email' => 'admin@ufp.test',
            'password_hash' => 'admin',
            'tipo_user' => TipoUtilizador::Admin, // Enum
            'edificio_id' => Edificio::where('sigla', 'UFP')->value('id'),
            'verificado_em' => now(),
        ]);

        Utilizador::create([
            'nome' => 'Estudante 1',
            'email' => 'estudante1@ufp.test',
            'password_hash' => 'estudante1',
            'tipo_user' => TipoUtilizador::Estudante, // Enum
            'verificado_em' => now(),
        ]);

        Utilizador::create([
            'nome' => 'Utilizador 2',
            'email' => 'estudante2@ufp.test',
            'password_hash' => 'estudante2',
            'tipo_user' => TipoUtilizador::Estudante, // Enum
            'verificado_em' => now(),
        ]);
    }
}
