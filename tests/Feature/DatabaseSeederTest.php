<?php

namespace Tests\Feature;

use App\Enums\Disponibilidade;
use App\Enums\EstadoReserva;
use App\Enums\TipoUtilizador;
use App\Models\Avaliacao;
use App\Models\Edificio;
use App\Models\Horario;
use App\Models\Ocupacao;
use App\Models\Reserva;
use App\Models\Sala;
use App\Models\Utilizador;
use Carbon\Carbon;
use Database\Seeders\AvaliacaoSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\EdificioSeeder;
use Database\Seeders\HorarioSeeder;
use Database\Seeders\OcupacaoSeeder;
use Database\Seeders\ReservaSeeder;
use Database\Seeders\SalaSeeder;
use Database\Seeders\UtilizadorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

        // 2. Utilizadores e hashes de password
        $this->assertDatabaseCount('utilizadores', 3);
        $admin = Utilizador::where('email', 'admin@ufp.test')->firstOrFail();
        $estudante1 = Utilizador::where('email', 'estudante1@ufp.test')->firstOrFail();
        $estudante2 = Utilizador::where('email', 'estudante2@ufp.test')->firstOrFail();

        $this->assertSame(TipoUtilizador::Admin, $admin->tipo_user);
        $this->assertTrue($admin->hasVerifiedEmail());
        $this->assertNotNull($admin->edificio_id);
        $this->assertTrue(Hash::check('admin', $admin->password_hash));

        $this->assertSame(TipoUtilizador::Estudante, $estudante1->tipo_user);
        $this->assertTrue(Hash::check('estudante1', $estudante1->password_hash));
        $this->assertTrue(Hash::check('estudante2', $estudante2->password_hash));

        // 3. Salas
        $this->assertDatabaseCount('salas', 3);
        $this->assertDatabaseHas('salas', [
            'nome_numero' => 'Sala 302',
            'tipo_sala' => 'SalaDeAula',
            'piso' => 3,
            'unidade_organica' => 'FCT',
            'disponibilidade' => Disponibilidade::Disponivel->value,
        ]);
        $this->assertDatabaseHas('salas', [
            'nome_numero' => 'Sala 204',
            'tipo_sala' => 'Trabalho',
            'piso' => 2,
            'unidade_organica' => 'FCHS',
            'disponibilidade' => Disponibilidade::Ocupado->value,
        ]);
        $this->assertDatabaseHas('salas', [
            'nome_numero' => 'Sala 101',
            'tipo_sala' => 'Exame',
            'piso' => 1,
            'unidade_organica' => 'ESS',
            'disponibilidade' => Disponibilidade::Disponivel->value,
        ]);

        // 4. Horários (08:00:00 até 18:00:00 -> 10 blocos de 1h)
        $this->assertDatabaseCount('horarios', 10);
        $this->assertDatabaseHas('horarios', ['hora_inicio' => '08:00:00', 'hora_fim' => '09:00:00']);
        $this->assertDatabaseHas('horarios', ['hora_inicio' => '17:00:00', 'hora_fim' => '18:00:00']);

        // 5. Reservas e regras de negócio
        $this->assertDatabaseCount('reservas', 2);
        $reservas = Reserva::with(['sala', 'horarios'])->get();
        $tiposValidos = ['AulaPratica', 'AulaTeorica', 'Estudo', 'Defesa', 'Palestra', 'Leitura', 'Outro', 'SalaDeEstudo', 'Laboratorio'];

        foreach ($reservas as $reserva) {
            $this->assertContains($reserva->tipo_de_uso, $tiposValidos);
            $this->assertFalse($reserva->data->isSunday(), 'Reserva seeded não pode cair ao Domingo.');
            $this->assertLessThanOrEqual($reserva->sala->capacidade_maxima, $reserva->quantidade_pessoas);
            $this->assertGreaterThanOrEqual(1, $reserva->horarios->count());
            $this->assertLessThanOrEqual(3, $reserva->horarios->count());

            // Valida que os blocos associados são consecutivos
            for ($i = 0; $i < $reserva->horarios->count() - 1; $i++) {
                $this->assertSame(
                    $reserva->horarios[$i]->hora_fim,
                    $reserva->horarios[$i + 1]->hora_inicio
                );
            }
        }

        // 6. Pivot reserva_horario (r1 tem 2 blocos, r2 tem 3 blocos = 5)
        $this->assertDatabaseCount('reserva_horario', 5);

        // 7. Avaliação ligada à reserva confirmada
        $this->assertDatabaseCount('avaliacoes', 1);
        $avaliacao = Avaliacao::with('reserva')->firstOrFail();
        $this->assertSame(5, $avaliacao->nota);
        $this->assertSame(EstadoReserva::Confirmada, $avaliacao->reserva->estado);

        // 8. Admin administra apenas as salas do seu edifício (UFP -> 2 salas)
        $this->assertDatabaseCount('administra', 2);
        $this->assertSame(2, $admin->salasAdministradas()->count());

        // 9. Ocupações
        $this->assertDatabaseCount('ocupacoes', 1);
        $ocupacao = Ocupacao::with('sala')->firstOrFail();
        $this->assertNotNull($ocupacao->sala);
        $this->assertInstanceOf(Carbon::class, $ocupacao->data_inicio);
        $this->assertInstanceOf(Carbon::class, $ocupacao->data_fim);
        $this->assertSame('Confirmada', $ocupacao->estado);
    }

    public function test_database_seeder_e_idempotente(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('edificios', 2);
        $this->assertDatabaseCount('utilizadores', 3);
        $this->assertDatabaseCount('salas', 3);
        $this->assertDatabaseCount('horarios', 10);
        $this->assertDatabaseCount('reservas', 2);
        $this->assertDatabaseCount('reserva_horario', 5);
        $this->assertDatabaseCount('avaliacoes', 1);
        $this->assertDatabaseCount('administra', 2);
        $this->assertDatabaseCount('ocupacoes', 1);
    }

    public function test_horario_seeder_gera_blocos_consecutivos_de_uma_hora(): void
    {
        $this->seed(HorarioSeeder::class);

        $horarios = Horario::orderBy('hora_inicio')->get();
        $this->assertCount(10, $horarios);
        $this->assertSame('08:00 - 09:00', $horarios->first()->intervalo);
        $this->assertSame('17:00 - 18:00', $horarios->last()->intervalo);

        for ($i = 0; $i < $horarios->count() - 1; $i++) {
            $this->assertSame($horarios[$i]->hora_fim, $horarios[$i + 1]->hora_inicio);
        }
    }

    public function test_sala_seeder_falha_graciosamente_sem_edificios(): void
    {
        $this->seed(SalaSeeder::class);

        $this->assertDatabaseCount('salas', 0);
    }

    public function test_reserva_seeder_falha_graciosamente_sem_dependencias(): void
    {
        $this->seed(EdificioSeeder::class);
        $this->seed(UtilizadorSeeder::class);
        $this->seed(SalaSeeder::class);
        // Sem HorarioSeeder
        $this->seed(ReservaSeeder::class);

        $this->assertDatabaseCount('reservas', 0);
    }

    public function test_avaliacao_seeder_falha_graciosamente_sem_reserva_confirmada(): void
    {
        $this->seed(EdificioSeeder::class);
        $this->seed(UtilizadorSeeder::class);
        $this->seed(SalaSeeder::class);

        $sala = Sala::firstOrFail();
        $estudante = Utilizador::where('tipo_user', TipoUtilizador::Estudante->value)->firstOrFail();

        // Apenas uma reserva pendente -> não deve ser avaliada
        Reserva::create([
            'data' => now()->subDay()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Pendente,
            'tipo_de_uso' => 'Estudo',
            'sala_id' => $sala->id,
            'utilizador_id' => $estudante->id,
        ]);

        $this->seed(AvaliacaoSeeder::class);

        $this->assertDatabaseCount('avaliacoes', 0);
    }

    public function test_ocupacao_seeder_falha_graciosamente_sem_salas(): void
    {
        $this->seed(OcupacaoSeeder::class);

        $this->assertDatabaseCount('ocupacoes', 0);
    }
}
