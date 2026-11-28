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

    protected function tearDown(): void
    {
        OcupacaoSeeder::$diretorioCustom = null;
        parent::tearDown();
    }

    private function criarSala(): Sala
    {
        $edificio = Edificio::create(['nome' => 'Edifício UFP', 'sigla' => 'UFP']);

        return Sala::create([
            'nome_numero' => 'Sala 302',
            'capacidade_maxima' => 30,
            'tipo_sala' => 'SalaDeAula',
            'piso' => 3,
            'unidade_organica' => 'FCT',
            'disponibilidade' => 'disponivel',
            'edificio_id' => $edificio->id,
        ]);
    }

    public function test_ocupacao_seeder_cria_ocupacao_padrao_quando_nao_ha_csvs(): void
    {
        $sala = $this->criarSala();
        $dirVazio = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ufp_vazio_' . uniqid();
        mkdir($dirVazio, 0777, true);

        try {
            OcupacaoSeeder::$diretorioCustom = $dirVazio;
            $this->seed(OcupacaoSeeder::class);

            $this->assertDatabaseCount('ocupacoes', 1);
            $ocupacao = Ocupacao::with('sala')->firstOrFail();

            $this->assertSame($sala->id, $ocupacao->sala->id);
            $this->assertSame('Aula', $ocupacao->tipo_ocupacao);
            $this->assertSame('Confirmada', $ocupacao->estado);
            $this->assertFalse($ocupacao->data_inicio->isSunday());
            $this->assertTrue($ocupacao->data_fim->greaterThan($ocupacao->data_inicio));
        } finally {
            if (is_dir($dirVazio)) {
                rmdir($dirVazio);
            }
        }
    }

    public function test_ocupacao_seeder_importa_ficheiros_csv_quando_existem(): void
    {
        $this->criarSala();

        $dirTmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ufp_csvs_' . uniqid();
        mkdir($dirTmp, 0777, true);
        $ficheiroTmp = $dirTmp . DIRECTORY_SEPARATOR . 'teste_ocupacoes.csv';
        file_put_contents($ficheiroTmp, "Data de Início;Data de Fim\n");

        try {
            $importadorMock = Mockery::mock(Importador::class);
            $importadorMock->shouldReceive('importar')
                ->once()
                ->with(Mockery::on(fn ($path) => basename($path) === 'teste_ocupacoes.csv'))
                ->andReturn(10);

            $this->app->instance(Importador::class, $importadorMock);

            OcupacaoSeeder::$diretorioCustom = $dirTmp;
            $this->seed(OcupacaoSeeder::class);
        } finally {
            if (file_exists($ficheiroTmp)) {
                unlink($ficheiroTmp);
            }
            if (is_dir($dirTmp)) {
                rmdir($dirTmp);
            }
        }
    }
}
