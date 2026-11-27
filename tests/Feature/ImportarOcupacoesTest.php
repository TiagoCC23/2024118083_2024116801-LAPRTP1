<?php

namespace Tests\Feature;

use App\Models\Edificio;
use App\Models\Ocupacao;
use App\Models\Sala;
use App\Services\Importador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class ImportarOcupacoesTest extends TestCase
{
    use RefreshDatabase;

    public function test_executa_comando_com_sucesso(): void
    {
        $importadorMock = Mockery::mock(Importador::class);
        $importadorMock->shouldReceive('importar')
            ->once()
            ->with('ficheiro_teste.csv')
            ->andReturn(15);

        $this->app->instance(Importador::class, $importadorMock);

        $this->artisan('ocupacoes:importar', ['ficheiro' => 'ficheiro_teste.csv'])
            ->expectsOutput('15 ocupações importadas.')
            ->assertExitCode(0);
    }

    public function test_comando_lida_com_runtime_exception(): void
    {
        $importadorMock = Mockery::mock(Importador::class);
        $importadorMock->shouldReceive('importar')
            ->once()
            ->with('ficheiro_invalido.csv')
            ->andThrow(new RuntimeException('Ficheiro não encontrado ou corrompido.'));

        $this->app->instance(Importador::class, $importadorMock);

        $this->artisan('ocupacoes:importar', ['ficheiro' => 'ficheiro_invalido.csv'])
            ->expectsOutput('Ficheiro não encontrado ou corrompido.')
            ->assertExitCode(1);
    }

    public function test_importador_importa_csv_e_evita_duplicados(): void
    {
        $edificio = Edificio::create(['nome' => 'Universidade Fernando Pessoa', 'sigla' => 'UFP']);
        $sala = Sala::create([
            'nome_numero' => 'Sala 302',
            'capacidade_maxima' => 30,
            'tipo_sala' => 'SalaDeAula',
            'piso' => 3,
            'disponibilidade' => 'disponivel',
            'edificio_id' => $edificio->id,
        ]);

        $conteudoUtf8 = implode("\n", [
            'Designação do Espaço;Sala 302 - Informática',
            'Data de Início;Data de Fim;Tipo de Ocupação;Código;Disciplina;Ciclo;Descrição;Estado;Responsável;Observações',
            '10-12-2026 09:00;10-12-2026 11:00;Aula;EI101;Programação Web;1º Ciclo;Aula prática;Confirmada;Prof. Silva;Trazer portátil',
            '10-12-2026 14:00;10-12-2026 16:00;Exame;EI202;Bases de Dados;1º Ciclo;;Confirmada;Prof. Costa;',
        ]);

        // Simula ficheiro em Windows-1252 como os exportados da UFP
        $ficheiroTmp = tempnam(sys_get_temp_dir(), 'ufp_csv_');
        file_put_contents($ficheiroTmp, mb_convert_encoding($conteudoUtf8, 'Windows-1252', 'UTF-8'));

        try {
            $importador = new Importador();

            $n1 = $importador->importar($ficheiroTmp);
            $this->assertSame(2, $n1);
            $this->assertDatabaseCount('ocupacoes', 2);

            $this->assertDatabaseHas('ocupacoes', [
                'sala_id' => $sala->id,
                'data_inicio' => '2026-12-10 09:00:00',
                'data_fim' => '2026-12-10 11:00:00',
                'tipo_ocupacao' => 'Aula',
                'codigo_disciplina' => 'EI101',
                'nome_disciplina' => 'Programação Web',
                'estado' => 'Confirmada',
                'pessoa_responsavel' => 'Prof. Silva',
            ]);

            // Reimportar o mesmo CSV não deve duplicar registos
            $importador->importar($ficheiroTmp);
            $this->assertDatabaseCount('ocupacoes', 2);
        } finally {
            if (file_exists($ficheiroTmp)) {
                unlink($ficheiroTmp);
            }
        }
    }

    public function test_importador_lanca_excecao_para_ficheiro_inexistente(): void
    {
        $this->expectException(RuntimeException::class);

        (new Importador())->importar('/caminho/inexistente/ficheiro.csv');
    }

    public function test_importador_lanca_excecao_quando_sala_nao_existe(): void
    {
        $ficheiroTmp = tempnam(sys_get_temp_dir(), 'ufp_csv_');
        file_put_contents($ficheiroTmp, "Designação do Espaço;Sala 999\nData de Início;Data de Fim\n");

        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('Sala não encontrada: Sala 999');

            (new Importador())->importar($ficheiroTmp);
        } finally {
            if (file_exists($ficheiroTmp)) {
                unlink($ficheiroTmp);
            }
        }
    }
}
