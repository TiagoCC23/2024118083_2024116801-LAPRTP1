<?php

namespace Tests\Feature;

use App\Models\Edificio;
use App\Models\Ocupacao;
use App\Models\Sala;
use App\Services\Importador;
use Database\Seeders\OcupacaoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OcupacaoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_ocupacao_seeder_cria_ocupacao_padrao_quando_nao_ha_csvs(): void
    {
        $edificio = Edificio::create(['nome' => 'Edifício UFP', 'sigla' => 'UFP']);
        $sala = Sala::create([
            'nome_numero' => 'Sala 302',
            'capacidade_maxima' => 30,
            'tipo_sala' => 'SalaDeAula',
            'piso' => 3,
            'unidade_organica' => 'FCT',
            'disponibilidade' => 'disponivel',
            'edificio_id' => $edificio->id,
        ]);

        $this->seed(OcupacaoSeeder::class);

        $this->assertDatabaseCount('ocupacoes', 1);
        $ocupacao = Ocupacao::with('sala')->firstOrFail();

        $this->assertSame($sala->id, $ocupacao->sala->id);
        $this->assertSame('Aula', $ocupacao->tipo_ocupacao);
        $this->assertSame('Confirmada', $ocupacao->estado);
        $this->assertFalse($ocupacao->data_inicio->isSunday());
        $this->assertTrue($ocupacao->data_fim->greaterThan($ocupacao->data_inicio));
    }

    public function test_ocupacao_seeder_importa_ficheiros_csv_quando_existem(): void
    {
        $diretorio = database_path('seeders/data');
        $diretorioCriado = !is_dir($diretorio);

        if ($diretorioCriado) {
            mkdir($diretorio, 0777, true);
        }

        $ficheiroTmp = $diretorio . '/teste_ocupacoes.csv';
        file_put_contents($ficheiroTmp, "Data de Início;Data de Fim\n");

        try {
            $importadorMock = Mockery::mock(Importador::class);
            $importadorMock->shouldReceive('importar')
                ->once()
                ->with($ficheiroTmp)
                ->andReturn(10);

            $this->app->instance(Importador::class, $importadorMock);

            $this->seed(OcupacaoSeeder::class);
        } finally {
            if (file_exists($ficheiroTmp)) {
                unlink($ficheiroTmp);
            }
            if ($diretorioCriado && is_dir($diretorio)) {
                rmdir($diretorio);
            }
        }
    }
}
