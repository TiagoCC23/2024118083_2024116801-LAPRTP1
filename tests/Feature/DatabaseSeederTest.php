<?php

namespace Tests\Feature;

use App\Enums\EstadoReserva;
use App\Models\Avaliacao;
use App\Models\Edificio;
use App\Models\Horario;
use App\Models\Reserva;
use App\Models\Sala;
use App\Models\Utilizador;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\EdificioSeeder;
use Database\Seeders\HorarioSeeder;
use Database\Seeders\SalaSeeder;
use Database\Seeders\UtilizadorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_executa_e_popula_toda_a_base_corretamente(): void
    {
        $this->seed(DatabaseSeeder::class);

        // 1. Edifícios
        $this->assertDatabaseCount('edificios', 2);
        $this->assertDatabaseHas('edificios', ['sigla' => 'UFP', 'nome' => 'Universidade Fernando Pessoa']);
        $this->assertDatabaseHas('edificios', ['sigla' => 'ESS', 'nome' => 'Escola Superior de Saúde Fernando Pessoa']);

        // 2. Utilizadores
        $this->assertDatabaseCount('utilizadores', 3);
        $this->assertDatabaseHas('utilizadores', ['email' => 'admin@ufp.test', 'tipo_user' => 'admin']);
        $this->assertDatabaseHas('utilizadores', ['email' => 'estudante1@ufp.test', 'tipo_user' => 'estudante']);
        $this->assertDatabaseHas('utilizadores', ['email' => 'estudante2@ufp.test', 'tipo_user' => 'estudante']);

        // 3. Salas
        $this->assertDatabaseCount('salas', 2);
        $this->assertDatabaseHas('salas', ['nome_numero' => 'Sala 302', 'piso' => 3]);
        $this->assertDatabaseHas('salas', ['nome_numero' => 'Sala 204', 'piso' => 2]);

        // 4. Horários (8h até às 18h -> 10 blocos de 1 hora)
        $this->assertDatabaseCount('horarios', 10);
        $this->assertDatabaseHas('horarios', ['hora_inicio' => '08:00', 'hora_fim' => '09:00']);
        $this->assertDatabaseHas('horarios', ['hora_inicio' => '17:00', 'hora_fim' => '18:00']);

        // 5. Reservas e Avaliações
        $this->assertDatabaseCount('reservas', 2);
        $this->assertDatabaseHas('reservas', [
            'tipo_de_uso' => 'Estudo individual',
            'estado' => EstadoReserva::Confirmada->value,
            'quantidade_pessoas' => 1,
        ]);
        $this->assertDatabaseHas('reservas', [
            'tipo_de_uso' => 'Trabalho de grupo',
            'estado' => EstadoReserva::Pendente->value,
            'quantidade_pessoas' => 4,
        ]);

        // 6. Vínculo da Avaliação com a primeira reserva
        $this->assertDatabaseCount('avaliacoes', 1);
        $this->assertDatabaseHas('avaliacoes', [
            'nota' => 5,
            'comentario' => 'Sala silenciosa e confortável.'
        ]);

        // 7. Horários associados às reservas (r1 tem 2 horários, r2 tem 3 horários = 5 registros pivot)
        $this->assertDatabaseCount('reserva_horario', 5);
    }

    public function test_horario_seeder_gera_exatamente_blocos_de_uma_hora(): void
    {
        $this->seed(HorarioSeeder::class);

        $horarios = Horario::orderBy('hora_inicio')->get();
        $this->assertCount(10, $horarios);

        $primeiro = $horarios->first();
        $this->assertSame('08:00 - 09:00', $primeiro->intervalo);

        $ultimo = $horarios->last();
        $this->assertSame('17:00 - 18:00', $ultimo->intervalo);
    }

    public function test_sala_seeder_falha_graciosamente_se_nao_existir_edificio(): void
    {
        // Roda o SalaSeeder sem ter rodado o EdificioSeeder
        $this->seed(SalaSeeder::class);

        // Nenhuma sala deve ter sido criada pois não havia edifício
        $this->assertDatabaseCount('salas', 0);
    }
}
