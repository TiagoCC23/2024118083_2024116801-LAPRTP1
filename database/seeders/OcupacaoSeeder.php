<?php

namespace Database\Seeders;

use App\Models\Ocupacao;
use App\Models\Sala;
use App\Services\Importador;
use Illuminate\Database\Seeder;
use RuntimeException;

class OcupacaoSeeder extends Seeder
{
    public static ?string $diretorioCustom = null;

    public function run(): void
    {
        if (Sala::doesntExist()) {
            $this->command?->warn('Nenhuma sala encontrada para criar ocupações.');
            return;
        }

        $diretorio = self::$diretorioCustom ?? database_path('seeders/data');
        $ficheirosCsv = is_dir($diretorio) ? (glob($diretorio . '/*.csv') ?: []) : [];

        if (!empty($ficheirosCsv)) {
            $importador = app(Importador::class);
            $totalImportadas = 0;

            foreach ($ficheirosCsv as $ficheiro) {
                try {
                    $totalImportadas += $importador->importar($ficheiro);
                } catch (RuntimeException $e) {
                    $this->command?->warn('CSV ignorado (' . basename($ficheiro) . '): ' . $e->getMessage());
                }
            }

            if ($totalImportadas > 0) {
                return;
            }
        }

        $sala = Sala::orderBy('id')->first();

        $diaUtil = now()->addDay();
        if ($diaUtil->isSunday()) {
            $diaUtil = $diaUtil->addDay();
        }

        Ocupacao::firstOrCreate(
            [
                'sala_id' => $sala->id,
                'data_inicio' => $diaUtil->copy()->setTime(9, 0)->format('Y-m-d H:i:s'),
                'data_fim' => $diaUtil->copy()->setTime(11, 0)->format('Y-m-d H:i:s'),
            ],
            [
                'tipo_ocupacao' => 'Aula',
                'codigo_disciplina' => 'EI101',
                'nome_disciplina' => 'Engenharia de Software',
                'ciclo_disciplina' => '1º Ciclo',
                'descricao_reserva' => 'Aula teórica semanal',
                'estado' => 'Confirmada',
                'pessoa_responsavel' => 'Docente UFP',
                'observacoes' => 'Equipamento multimédia necessário.',
            ]
        );
    }
}
