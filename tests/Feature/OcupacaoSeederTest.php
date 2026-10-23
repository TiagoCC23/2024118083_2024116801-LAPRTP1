<?php

namespace Tests\Feature;

use App\Services\Importador;
use Database\Seeders\OcupacaoSeeder;
use Mockery;
use Tests\TestCase;

class OcupacaoSeederTest extends TestCase
{
    public function test_ocupacao_seeder_invoca_importador_com_caminho_dos_csvs(): void
    {
        $caminhoEsperado = database_path('seeders/data');

        $importadorMock = Mockery::mock(Importador::class);
        $importadorMock->shouldReceive('importar')
            ->once()
            ->with($caminhoEsperado)
            ->andReturn(100);

        $this->app->instance(Importador::class, $importadorMock);

        $this->seed(OcupacaoSeeder::class);
    }
}
