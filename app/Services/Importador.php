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
        if (!is_file($caminho) || !is_readable($caminho)) {
            throw new RuntimeException('Não foi possível abrir o ficheiro CSV.');
        }

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
                // Converte de Windows-1252 para UTF-8 se necessário
                $linhaUtf8 = mb_check_encoding($linhaRaw, 'UTF-8')
                    ? $linhaRaw
                    : mb_convert_encoding($linhaRaw, 'UTF-8', 'Windows-1252');

                $colunas = str_getcsv(trim($linhaUtf8), ';');
                $primeiraColuna = trim($colunas[0] ?? '');

                // Lê metadados antes do cabeçalho da tabela
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

                // Lê as linhas de ocupação
                if (count($colunas) < 8 || empty(trim($colunas[0])) || empty(trim($colunas[1]))) {
                    continue;
                }

                try {
                    $inicio = Carbon::createFromFormat('d-m-Y H:i', trim($colunas[0]));
                    $fim = Carbon::createFromFormat('d-m-Y H:i', trim($colunas[1]));
                } catch (\Throwable) {
                    continue;
                }

                if (!$inicio || !$fim) {
                    continue;
                }

                $limpar = fn (int $idx): ?string => isset($colunas[$idx]) && trim($colunas[$idx]) !== ''
                    ? trim($colunas[$idx])
                    : null;

                Ocupacao::firstOrCreate(
                    [
                        'sala_id'     => $sala->id,
                        'data_inicio' => $inicio->format('Y-m-d H:i:s'),
                        'data_fim'    => $fim->format('Y-m-d H:i:s'),
                    ],
                    [
                        'tipo_ocupacao'      => $limpar(2) ?? 'Aula',
                        'codigo_disciplina'  => $limpar(3),
                        'nome_disciplina'    => $limpar(4),
                        'ciclo_disciplina'   => $limpar(5),
                        'descricao_reserva'  => $limpar(6),
                        'estado'             => $limpar(7) ?? 'Confirmada',
                        'pessoa_responsavel' => $limpar(8),
                        'observacoes'        => $limpar(9),
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

        $designacaoLower = mb_strtolower($designacao);
        $semPrefixoLower = mb_strtolower($semPrefixo);

        // 1. Match exato (case-insensitive, compatível com Postgres e SQLite)
        $sala = Sala::whereRaw('LOWER(nome_numero) = ?', [$designacaoLower])
            ->orWhereRaw('LOWER(nome_numero) = ?', [$semPrefixoLower])
            ->first();

        if ($sala) {
            return $sala;
        }

        // 2. Extrai código se for número (ex: "302", "204A") ou sigla em MAIÚSCULAS
        if (preg_match('/^Sala\s+([0-9]+[A-Za-z]?|[A-Z]{2,})(?:\s*-|\s|$)/', $designacao, $matches)) {
            $codigoLower = mb_strtolower($matches[1]);

            $salaPorCodigo = Sala::whereRaw('LOWER(nome_numero) = ?', [$codigoLower])
                ->orWhereRaw('LOWER(nome_numero) = ?', ['sala ' . $codigoLower])
                ->orWhereRaw('LOWER(nome_numero) LIKE ?', ["%{$codigoLower}%"])
                ->first();

            if ($salaPorCodigo) {
                return $salaPorCodigo;
            }
        }

        // 3. Fallback: contém o texto sem prefixo
        return Sala::whereRaw('LOWER(nome_numero) LIKE ?', ["%{$semPrefixoLower}%"])->first();
    }
}
