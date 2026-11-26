<?php

namespace Database\Seeders;

use App\Enums\TipoUtilizador;
use App\Models\Edificio;
use App\Models\Utilizador;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UtilizadorSeeder extends Seeder
{
    public function run(): void
    {
        Utilizador::create([
            'nome' => env('SEED_ADMIN_NOME', 'Admin UFP'),
            'email' => env('SEED_ADMIN_EMAIL', 'admin@ufp.test'),
            'password_hash' =>  Hash::make(env('SEED_ADMIN_PASSWORD', 'admin')),
            'tipo_user' => TipoUtilizador::Admin, // Enum
            'edificio_id' => Edificio::where('sigla', 'UFP')->value('id'),
            'verificado_em' => now(),
        ]);

        Utilizador::create([
            'nome' => env('SEED_ESTUDANTE1_NOME', 'Estudante 1'),
            'email' => env('SEED_ESTUDANTE1_EMAIL', 'estudante1@ufp.test'),
            'password_hash' => env('SEED_ESTUDANTE1_PASSWORD', 'estudante1'),
            'tipo_user' => TipoUtilizador::Estudante, // Enum
            'verificado_em' => now(),
        ]);

        Utilizador::create([
            'nome' => env('SEED_ESTUDANTE2_NOME', 'Estudante 2'),
            'email' => env('SEED_ESTUDANTE2_EMAIL', 'estudante2@ufp.test'),
            'password_hash' => env('SEED_ESTUDANTE2_PASSWORD', 'estudante2'),
            'tipo_user' => TipoUtilizador::Estudante, // Enum
            'verificado_em' => now(),
        ]);
    }
}
