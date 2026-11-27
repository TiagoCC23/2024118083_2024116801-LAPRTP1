<?php

namespace App\Services;

use App\Models\Ocupacao;
use App\Models\Sala;
use Carbon\Carbon;
use RuntimeException;

class Importador
{
    public function importar(string $caminho): int
    {
        $handle = fopen($caminho, 'r');
        if (!$handle) {
            throw new RuntimeException('Não foi possível abrir o ficheiro CSV.');
        }

        $designacaoEspaco = null;
        $headerEncontrado = false;
        $sala = null;
        $importadas = 0;

        try {
            while (($linhaRaw = fgets($handle)) !== false) {
                /* Converte de Win1252 (que é o formato atual) para UTF-8 (o Postgres só funciona com UTF-8 */
                $linhaUtf8 = mb_check_encoding($linhaRaw, 'UTF-8') ? $linhaRaw : mb_convert_encoding($linhaRaw, 'UTF-8', 'Windows-1252');

                $colunas = str_getcsv(trim($linhaUtf8), ';');
                $primeiraColuna = trim($colunas[0] ?? '');

                /* Le metadados antes de começar a ler a tabela*/
                if (!$headerEncontrado) {
                    if (str_contains($primeiraColuna, 'Designação do Espaço')) {
                        $designacaoEspaco = trim($colunas[1] ?? '');
                    }

                    if ($primeiraColuna === 'Data de Início') {
                        $headerEncontrado = true;
                        $sala = $this->encontrarSalaCsv($designacaoEspaco);

                        if (!$sala) {
                            throw new RuntimeException("Sala não encontrada: {$designacaoEspaco}");
                        }
                    }
                    continue;
                }

                /* Le as linhas de ocupação*/
                if (count($colunas) < 8 || empty($colunas[0]) || empty($colunas[1])) {
                    continue;
                }

                $inicio = Carbon::createFromFormat('d-m-Y H:i', trim($colunas[0]));
                $fim    = Carbon::createFromFormat('d-m-Y H:i', trim($colunas[1]));

                Ocupacao::firstOrCreate(
                    [
                        'sala_id'     => $sala->id,
                        'data_inicio' => $inicio->format('Y-m-d H:i:s'),
                        'data_fim'    => $fim->format('Y-m-d H:i:s'),
                    ],
                    [
                        'tipo_ocupacao' => trim($colunas[2] ?? 'Aula'),
                        'descricao'     => trim($colunas[4] ?? $colunas[6] ?? ''),
                    ]
                );

                $importadas++;
            }
        } finally {
            fclose($handle);
        }

        return $importadas;
    }
    private function encontrarSalaCsv(?string $designacao): ?Sala
    {
        if (!$designacao) {
            return null;
        }

        $designacao = trim($designacao);
        $semPrefixo = trim(preg_replace('/^Sala\s+/i', '', $designacao));

        /* Faz o match direto pelo nome */
        $sala = Sala::where('nome', 'ILIKE', $designacao)
            ->orWhere('nome', 'ILIKE', $semPrefixo)
            ->first();

        if ($sala) {
            return $sala;
        }

        /* Extrai código se for número OU sigla em MAIÚSCULAS */
        if (preg_match('/^Sala\s+([0-9]+[A-Za-z]?|[A-Z]{2,})(?:\s*-|\s|$)/', $designacao, $matches)) {
            $codigo = $matches[1];

            return Sala::where(function ($query) use ($codigo) {
                if (is_numeric($codigo)) {
                    $query->where('numero', $codigo);
                }
                $query->orWhere('nome', 'ILIKE', "%{$codigo}%");
            })->first();
        }

        return Sala::where('nome', 'ILIKE', "%{$semPrefixo}%")->first();
    }

}
