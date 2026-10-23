<?php

namespace Tests\Feature;

use App\Services\Importador;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class ImportarOcupacoesTest extends Importador
{
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
}
