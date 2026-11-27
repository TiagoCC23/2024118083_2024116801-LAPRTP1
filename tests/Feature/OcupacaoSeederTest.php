<?php

namespace Tests\Feature;

use App\Models\Ocupacao;
use App\Services\Importador;
use Database\Seeders\EdificioSeeder;
use Database\Seeders\OcupacaoSeeder;
use Database\Seeders\SalaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OcupacaoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_ocupacao_seeder_popula_ocupacoes_a_partir_dos_csvs_ou_padrao(): void
    {
        $this->seed(EdificioSeeder::class);
        $this->seed(SalaSeeder::class);
        $this->seed(OcupacaoSeeder::class);

        $this->assertGreaterThan(0, Ocupacao::count());

        $ocupacao = Ocupacao::with('sala')->firstOrFail();

        $this->assertNotNull($ocupacao->sala);
        $this->assertSame('Aula', $ocupacao->tipo_ocupacao);
        $this->assertSame('Confirmada', $ocupacao->estado);
        $this->assertFalse($ocupacao->data_inicio->isSunday());
        $this->assertTrue($ocupacao->data_fim->greaterThan($ocupacao->data_inicio));
    }

    public function test_ocupacao_seeder_importa_todos_os_ficheiros_csv_quando_existem(): void
    {
        $diretorio = database_path('seeders/data');
        $ficheirosCsv = is_dir($diretorio) ? (glob($diretorio . '/*.csv') ?: []) : [];

        $this->assertNotEmpty($ficheirosCsv);

        $importadorMock = Mockery::mock(Importador::class);
        foreach ($ficheirosCsv as $ficheiro) {
            $importadorMock->shouldReceive('importar')
                ->once()
                ->with($ficheiro)
                ->andReturn(1);
        }

        $this->app->instance(Importador::class, $importadorMock);

        $this->seed(OcupacaoSeeder::class);
    }
}
