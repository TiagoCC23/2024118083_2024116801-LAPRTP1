<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sala extends Model
{
    protected $table = 'salas';
    public $timestamps = false;

    protected $fillable = [
        'nome_numero',
        'capacidade_maxima',
        'tipo_sala',
        'disponibilidade',
        'piso',
        'unidade_organica',
        'edificio_id',
    ];

    public function edificio(): BelongsTo
    {
        return $this->belongsTo(Edificio::class);
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    public function ocupacoes(): HasMany
    {
        return $this->hasMany(Ocupacao::class);
    }

    public function administradores(): BelongsToMany
    {
        return $this->belongsToMany(Utilizador::class, 'administra', 'sala_id', 'utilizador_id');
    }

    public function getTipoSalaLabelAttribute(): string
    {
        return match ($this->tipo_sala) {
            'SalaDeAula', 'Normal' => 'Sala de Aula',
            'Trabalho' => 'Trabalho',
            'Exame' => 'Exame',
            'SalaDeEstudo' => 'Sala de Estudo',
            'Laboratorio' => 'Laboratório',
            'SalaoNobre' => 'Salão Nobre',
            'Outro' => 'Outro',
            default => $this->tipo_sala,
        };
    }
}
