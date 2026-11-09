<?php

namespace App\Console\Commands;

use App\Services\ImportadorOcupacoes;
use Illuminate\Console\Command;
use RuntimeException;

class ImportarOcupacoes extends Command
{
    protected $signature = 'ocupacoes:importar {ficheiro}';
    protected $description = 'Importa o CSV de ocupação de um espaço';

    public function handle(Importador $importador): int
    {
        try {
            $n = $importador->importar($this->argument('ficheiro'));
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("{$n} ocupações importadas.");

        return self::SUCCESS;
    }
}
