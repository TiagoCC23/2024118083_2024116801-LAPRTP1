<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ocupacao extends Model
{
    protected $table = 'ocupacoes';
    public $timestamps = false;
    protected $fillable = [
        'data_inicio', 'data_fim', 'tipo_ocupacao', 'codigo_disciplina', 'nome_disciplina',
        'ciclo_disciplina', 'descricao_reserva', 'estado', 'pessoa_responsavel', 'observacoes', 'sala_id',
    ];
    protected $casts = ['data_inicio' => 'datetime', 'data_fim' => 'datetime'];

    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class);
    }
}
