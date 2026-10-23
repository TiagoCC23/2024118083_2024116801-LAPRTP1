<?php

namespace Database\Seeders;

use App\Models\Edificio;
use App\Models\Sala;
use App\Models\Utilizador;
use Illuminate\Database\Seeder;

class SalaSeeder extends Seeder
{
    public function run(): void
    {
        $edificio = Edificio::where('sigla', 'FCH')->first() ?? Edificio::first();

        if (!$edificio) {
            $this->command->error('Nenhum edifício foi encontrado! Execute o EdificioSeeder primeiro.'); // debug, dps tira
            return;
        }

        $s1 = Sala::create([
            'nome_numero' => 'Sala 302',
            'tipo_sala' => 'Sala de Aula',
            'capacidade_maxima' => 30,
            'piso' => 3,
            'edificio_id' => $edificio->id,
        ]);

        $s2 = Sala::create([
            'nome_numero' => 'Sala 204',
            'tipo_sala' => 'Sala de Aula',
            'capacidade_maxima' => 30,
            'piso' => 2,
            'edificio_id' => $edificio->id,
        ]);

        $admin = Utilizador::where('tipo_user', 'admin')->first();

        if ($admin && $admin->edificio_id) {
            $admin->salasAdministradas()->sync(
                Sala::where('edificio_id', $admin->edificio_id)->pluck('id')
            );
        }
    }
}
