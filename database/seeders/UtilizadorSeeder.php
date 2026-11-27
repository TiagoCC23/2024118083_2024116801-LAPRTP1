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
        $edificioUfpId = Edificio::where('sigla', 'UFP')->value('id');

        Utilizador::firstOrCreate(
            ['email' => env('SEED_ADMIN_EMAIL', 'admin@ufp.test')],
            [
                'nome' => env('SEED_ADMIN_NOME', 'Admin UFP'),
                'password_hash' => env('SEED_ADMIN_PASSWORD', 'admin'),
                'tipo_user' => TipoUtilizador::Admin,
                'edificio_id' => $edificioUfpId,
                'verificado_em' => now(),
            ]
        );

        Utilizador::firstOrCreate(
            ['email' => env('SEED_ESTUDANTE1_EMAIL', 'estudante1@ufp.test')],
            [
                'nome' => env('SEED_ESTUDANTE1_NOME', 'Estudante 1'),
                'password_hash' => env('SEED_ESTUDANTE1_PASSWORD', 'estudante1'),
                'tipo_user' => TipoUtilizador::Estudante,
                'edificio_id' => $edificioUfpId,
                'verificado_em' => now(),
            ]
        );

        Utilizador::firstOrCreate(
            ['email' => env('SEED_ESTUDANTE2_EMAIL', 'estudante2@ufp.test')],
            [
                'nome' => env('SEED_ESTUDANTE2_NOME', 'Estudante 2'),
                'password_hash' => env('SEED_ESTUDANTE2_PASSWORD', 'estudante2'),
                'tipo_user' => TipoUtilizador::Estudante,
                'edificio_id' => Edificio::where('sigla', 'ESS')->value('id'),
                'verificado_em' => now(),
            ]
        );
    }
}
