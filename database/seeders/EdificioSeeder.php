<?php

namespace Database\Seeders;

use App\Models\Edificio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EdificioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([['nome' => 'Universidade Fernando Pessoa', 'sigla' => 'UFP', 'imagem' => 'Sede.png'],
                  ['nome' => 'Escola Superior de Saúde Fernando Pessoa', 'sigla' => 'ESS', 'imagem' => 'Saude.png'],
                 ] as $edificio) {
            Edificio::create($edificio);
        }
    }
}
