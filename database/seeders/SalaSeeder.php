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
        $salasCsvUfp = [
            // Piso 1
            ['nome_numero' => 'Sala 101- Estiradores', 'tipo_sala' => 'Trabalho', 'capacidade_maxima' => 25, 'piso' => 1, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 102 - Estiradores', 'tipo_sala' => 'Trabalho', 'capacidade_maxima' => 25, 'piso' => 1, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 103 - Estiradores', 'tipo_sala' => 'Trabalho', 'capacidade_maxima' => 25, 'piso' => 1, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 104', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 1, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 105', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 1, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 107 - Estiradores', 'tipo_sala' => 'Trabalho', 'capacidade_maxima' => 25, 'piso' => 1, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 108 - Sala Arquitetura', 'tipo_sala' => 'Trabalho', 'capacidade_maxima' => 25, 'piso' => 1, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 109', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 1, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 111', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 1, 'unidade_organica' => 'FCT'],

            // Piso 2 (a Sala 204 já está acima)
            ['nome_numero' => 'Sala 201', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 2, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 202', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 2, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 203', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 2, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 205', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 2, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 206', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 2, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 207', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 2, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 208', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 2, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 209', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 2, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 210', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 2, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 211', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 2, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 212', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 2, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 213', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 2, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala 214', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 2, 'unidade_organica' => 'FCT'],

            // Piso 3 (a Sala 302 já está acima)
            ['nome_numero' => 'Sala 309', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 30, 'piso' => 3, 'unidade_organica' => 'FCT'],

            // Auditórios, Laboratórios e Outros
            ['nome_numero' => 'Sala A1', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 120, 'piso' => 0, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala A2', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 120, 'piso' => 0, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala AEP - Laboratório Publicidade', 'tipo_sala' => 'Trabalho', 'capacidade_maxima' => 25, 'piso' => 0, 'unidade_organica' => 'FCHS'],
            ['nome_numero' => 'Sala AVI - Audiovisuais', 'tipo_sala' => 'Trabalho', 'capacidade_maxima' => 25, 'piso' => 0, 'unidade_organica' => 'FCHS'],
            ['nome_numero' => 'Gabinete do Docente da FCHS', 'tipo_sala' => 'Trabalho', 'capacidade_maxima' => 10, 'piso' => 1, 'unidade_organica' => 'FCHS'],
            ['nome_numero' => 'Sala GIN - Pavilhão Gimnodesportivo', 'tipo_sala' => 'Trabalho', 'capacidade_maxima' => 50, 'piso' => 0, 'unidade_organica' => 'FCT'],
            ['nome_numero' => 'Sala LRA - Laboratório Radio', 'tipo_sala' => 'Trabalho', 'capacidade_maxima' => 15, 'piso' => 0, 'unidade_organica' => 'FCHS'],
            ['nome_numero' => 'Sala Pós- Graduação (Sede)', 'tipo_sala' => 'SalaDeAula', 'capacidade_maxima' => 25, 'piso' => 3, 'unidade_organica' => 'FCHS'],
        ];

        foreach ($salasCsvUfp as $salaCsv) {
            $salas[] = [
                'edificio_id' => $edificioUfp->id,
                'nome_numero' => $salaCsv['nome_numero'],
                'tipo_sala' => $salaCsv['tipo_sala'],
                'capacidade_maxima' => $salaCsv['capacidade_maxima'],
                'piso' => $salaCsv['piso'],
                'unidade_organica' => $salaCsv['unidade_organica'],
                'disponibilidade' => Disponibilidade::Disponivel->value,
            ];
        }

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
