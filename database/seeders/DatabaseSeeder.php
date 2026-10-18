<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Edificio;
use App\Models\Sala;
use App\Models\Horario;
use App\Models\Reserva;
use App\Models\Avaliacao;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // EDIFICIOS
        $edificio_Sede = Edificio::create([
            'nome' => 'Universidade Fernando Pessoa',
            'sigla' => 'UFP',
            'imagem' => 'planta_sede.png'
        ]);
        $edificio_Ciencias = Edificio::create([
            'nome' => 'Faculdade de Ciência e Tecnologia',
            'sigla' => 'FCT',
            'imagem' => 'planta_fct.png'
        ]);

        // SALAS
        $sala1 = Sala::create([
            'nome_numero' => '204',
            'capacidade_maxima' => '40',
            'tipo_sala' => 'Sala de aula',
            'edificio_id' => $edificio_Sede->id
        ]);
        $sala2 = Sala::create([
            'nome_numero' => '1',
            'capacidade_maxima' => '10',
            'tipo_sala' => 'Sala de estudo',
            'edificio_id' => $edificio_Sede->id
        ]);
        $sala3 = Sala::create([
            'nome_numero' => 'EC2.4',
            'capacidade_maxima' => '10',
            'tipo_sala' => 'Laboratório',
            'edificio_id' => $edificio_Ciencias->id
        ]);

        // UTILIZADORES
        $admin_sede = User::create([
            'nome' => 'Funcionário Sede',
            'email' => 'admin.sede@ufp.edu.pt',
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'tipo_user' => 'admin',
            'edificio_id' => $edificio_Sede->id
        ]);
        $user1 = User::create([
            'nome' => 'Rayssa Santos',
            'email' => '2024118083@ufp.edu.pt',
            'password' => Hash::make('password123'),
            'tipo_user' => 'estudante',
        ]);
        $user2 = User::create([
            'nome' => 'Tiago Chousal',
            'email' => '2024116801@ufp.edu.pt',
            'password' => Hash::make('67676767'),
            'tipo_user' => 'estudante',
        ]);

        // HORARIOS
        $h1 = Horario::create(['hora_inicio' => '09:00:00', 'hora_fim' => '10:00:00']);
        $h2 = Horario::create(['hora_inicio' => '10:00:00', 'hora_fim' => '11:00:00']);
        $h3 = Horario::create(['hora_inicio' => '11:00:00', 'hora_fim' => '12:00:00']);

        $reserva = Reserva::create([
            'data' => now()->addDays(2)->toDateString(),
            'quantidade_pessoas' => 4,
            'estado' => 'confirmada',
            'tipo_de_uso' => 'Trabalho de grupo',
            'sala_id' => $sala3->id,
            'utilizador_id' => $user1->id,
        ]);

        $reserva->horarios()->attach([$h1->id, $h2->id]);

        // AVALIAÇÃO
        $avaliacao = Avaliacao::create([
            'nota' => 5,
            'comentario' => 'Excelente sala para reunir em grupo, boa iluminação.',
            'id_reserva' => $reserva->id,
        ]);
    }
}
