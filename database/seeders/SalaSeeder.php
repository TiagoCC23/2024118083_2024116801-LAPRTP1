<?php

namespace Database\Seeders;

use App\Enums\Disponibilidade;
use App\Enums\TipoUtilizador;
use App\Models\Edificio;
use App\Models\Sala;
use App\Models\Utilizador;
use Illuminate\Database\Seeder;

class SalaSeeder extends Seeder
{
    public function run(): void
    {
        $edificioUfp = Edificio::where('sigla', 'UFP')->first() ?? Edificio::first();

        if (!$edificioUfp) {
            $this->command?->error('Nenhum edifício foi encontrado! Execute o EdificioSeeder primeiro.');
            return;
        }

        $salas = [
            [
                'edificio_id' => $edificioUfp->id,
                'nome_numero' => 'Sala 302',
                'tipo_sala' => 'SalaDeAula',
                'capacidade_maxima' => 30,
                'piso' => 3,
                'unidade_organica' => 'FCT',
                'disponibilidade' => Disponibilidade::Disponivel->value,
            ],
            [
                'edificio_id' => $edificioUfp->id,
                'nome_numero' => 'Sala 204',
                'tipo_sala' => 'Trabalho',
                'capacidade_maxima' => 20,
                'piso' => 2,
                'unidade_organica' => 'FCHS',
                'disponibilidade' => Disponibilidade::Ocupado->value,
            ],
        ];

        $edificioEss = Edificio::where('sigla', 'ESS')->first();
        if ($edificioEss) {
            $salas[] = [
                'edificio_id' => $edificioEss->id,
                'nome_numero' => 'Sala 101',
                'tipo_sala' => 'Exame',
                'capacidade_maxima' => 40,
                'piso' => 1,
                'unidade_organica' => 'ESS',
                'disponibilidade' => Disponibilidade::Disponivel->value,
            ];
        }

        foreach ($salas as $sala) {
            Sala::firstOrCreate(
                [
                    'edificio_id' => $sala['edificio_id'],
                    'nome_numero' => $sala['nome_numero'],
                ],
                $sala
            );
        }

        $admin = Utilizador::where('tipo_user', TipoUtilizador::Admin->value)->first();

        if ($admin && $admin->edificio_id) {
            $admin->salasAdministradas()->sync(
                Sala::where('edificio_id', $admin->edificio_id)->pluck('id')
            );
        }
    }
}
