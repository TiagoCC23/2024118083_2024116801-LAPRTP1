<?php

namespace App\Models;

use App\Models\Concerns\Atualizacoes;
use Illuminate\Database\Eloquent\Model;
use App\Enums\EstadoReserva;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reserva extends Model
{
    use Atualizacoes;
    protected $casts = [
        'data' => 'date',
        'estado' => EstadoReserva::class,
    ];
    protected $table = 'reservas';
    protected $fillable = ['data', 'quantidade_pessoas', 'estado', 'tipo_de_uso', 'sala_id', 'utilizador_id'];

    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class);
    }

    public function utilizador(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function horarios(): BelongsToMany
    {
        return $this->belongsToMany(Horario::class, 'horario_reserva', 'reserva_id', 'horario_id')
            ->orderBy('hora_inicio');
    }

    public function avaliacao(): HasOne
    {
        return $this->hasOne(Avaliacao::class);
    }
}
