<?php

namespace Tests\Feature;

use App\Enums\EstadoReserva;
use App\Models\Avaliacao;
use App\Models\Edificio;
use App\Models\Horario;
use App\Models\Reserva;
use App\Models\Sala;
use App\Models\Utilizador;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_todas_as_tabelas_do_sistema_foram_criadas(): void
    {
        $tabelas = [
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'edificios',
            'utilizadores',
            'salas',
            'horarios',
            'reservas',
            'reserva_horario',
            'avaliacoes',
            'administra',
            'ocupacoes',
        ];

        foreach ($tabelas as $tabela) {
            $this->assertTrue(Schema::hasTable($tabela), "A tabela '{$tabela}' não foi encontrada.");
        }
    }

    public function test_coluna_unidade_organica_foi_adicionada_em_salas(): void
    {
        $this->assertTrue(Schema::hasColumn('salas', 'unidade_organica'));
    }

    public function test_colunas_customizadas_de_utilizador_e_reserva_existem(): void
    {
        $this->assertTrue(Schema::hasColumns('utilizadores', [
            'id', 'nome', 'email', 'verificado_em', 'password_hash', 'tipo_user', 'edificio_id', 'criado_em', 'eliminado_em'
        ]));

        $this->assertTrue(Schema::hasColumns('reservas', [
            'id', 'data', 'quantidade_pessoas', 'estado', 'tipo_de_uso', 'sala_id', 'utilizador_id', 'criado_em', 'eliminado_em'
        ]));

        $this->assertTrue(Schema::hasColumns('ocupacoes', [
            'id', 'data_inicio', 'data_fim', 'tipo_ocupacao', 'codigo_disciplina', 'nome_disciplina', 'sala_id'
        ]));
    }

    public function test_restricao_unique_impede_duplicacao_de_sala_no_mesmo_edificio(): void
    {
        $edificio = Edificio::create(['nome' => 'Sede', 'sigla' => 'SED']);

        Sala::create([
            'nome_numero' => 'Sala 101',
            'capacidade_maxima' => 30,
            'tipo_sala' => 'Aula',
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);

        $this->expectException(QueryException::class);

        // Tentar criar com o mesmo nome e mesmo edificio deve lançar exceção
        Sala::create([
            'nome_numero' => 'Sala 101',
            'capacidade_maxima' => 25,
            'tipo_sala' => 'Laboratório',
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);
    }

    public function test_restricao_unique_impede_horarios_duplicados(): void
    {
        Horario::create(['hora_inicio' => '08:00', 'hora_fim' => '09:00']);

        $this->expectException(QueryException::class);

        Horario::create(['hora_inicio' => '08:00', 'hora_fim' => '09:00']);
    }

    public function test_edificio_com_sala_nao_pode_ser_eliminado_devido_ao_restrict_on_delete(): void
    {
        $edificio = Edificio::create(['nome' => 'Polo II', 'sigla' => 'POL2']);

        Sala::create([
            'nome_numero' => 'Sala 201',
            'capacidade_maxima' => 20,
            'tipo_sala' => 'Aula',
            'piso' => 2,
            'edificio_id' => $edificio->id,
        ]);

        $this->expectException(QueryException::class);
        $edificio->delete();
    }

    public function test_eliminacao_de_reserva_remove_avaliacao_e_pivot_em_cascata(): void
    {
        $edificio = Edificio::create(['nome' => 'Campus', 'sigla' => 'CMP']);
        $sala = Sala::create([
            'nome_numero' => 'Sala 10',
            'capacidade_maxima' => 15,
            'tipo_sala' => 'Reunião',
            'piso' => 1,
            'edificio_id' => $edificio->id,
        ]);
        $user = Utilizador::create([
            'nome' => 'Aluno',
            'email' => 'aluno@ufp.test',
            'password_hash' => '123456',
            'tipo_user' => 'estudante',
        ]);
        $reserva = Reserva::create([
            'data' => now()->toDateString(),
            'quantidade_pessoas' => 2,
            'estado' => EstadoReserva::Confirmada,
            'tipo_de_uso' => 'Estudo',
            'sala_id' => $sala->id,
            'utilizador_id' => $user->id,
        ]);
        $horario = Horario::create(['hora_inicio' => '14:00', 'hora_fim' => '15:00']);
        $reserva->horarios()->attach($horario->id);

        Avaliacao::create([
            'reserva_id' => $reserva->id,
            'nota' => 4,
            'comentario' => 'Bom',
        ]);

        // Apaga a reserva diretamente via SQL
        $reserva->delete();

        $this->assertDatabaseMissing('reservas', ['id' => $reserva->id]);
        $this->assertDatabaseMissing('avaliacoes', ['reserva_id' => $reserva->id]);
        $this->assertDatabaseMissing('reserva_horario', ['reserva_id' => $reserva->id]);
    }

    public function test_rollback_e_reexecucao_das_migrations(): void
    {
        $this->artisan('migrate:rollback')->assertExitCode(0);
        $this->artisan('migrate')->assertExitCode(0);

        $this->assertTrue(Schema::hasTable('reservas'));
    }
}
