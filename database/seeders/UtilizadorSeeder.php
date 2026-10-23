<?php

namespace Database\Seeders;

use App\Models\Edificio;
use App\Models\Utilizador;
use Illuminate\Database\Seeder;

class UtilizadorSeeder extends Seeder
{
    public function run(): void
    {
        $u1 = Utilizador::create([
            'nome' => 'Admin Biblioteca', 'email' => 'admin@ufp.test', 'password_hash' => 'admin',
            'tipo_user' => 'admin', 'edificio_id' => Edificio::where('sigla', 'BIB')->value('id'),
            'verificado_em' => now(),
        ]);

        $u2 = Utilizador::create([
            'nome' => 'Estudante 1', 'email' => 'estudante1@ufp.test', 'password_hash' => 'estudante1',
            'tipo_user' => 'estudante', 'verificado_em' => now(),
        ]);

        $u3 = Utilizador::create([
            'nome' => 'Utilizador 2', 'email' => 'estudante2@ufp.test', 'password_hash' => 'estudante2',
            'tipo_user' => 'estudante', 'verificado_em' => now(),
        ]);
    }
}
