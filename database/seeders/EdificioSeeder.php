<?php

namespace Database\Seeders;

use App\Models\Edificio;
use Illuminate\Database\Seeder;

class EdificioSeeder extends Seeder
{
    public function run(): void
    {
        $edificios = [
            [
                'nome' => 'Universidade Fernando Pessoa',
                'sigla' => 'UFP',
                'imagem' => 'Sede.png',
            ],
            [
                'nome' => 'Escola Superior de Saúde Fernando Pessoa',
                'sigla' => 'ESS',
                'imagem' => 'Saude.png',
            ],
        ];

        foreach ($edificios as $edificio) {
            Edificio::firstOrCreate(
                ['sigla' => $edificio['sigla']],
                $edificio
            );
        }
    }
}
