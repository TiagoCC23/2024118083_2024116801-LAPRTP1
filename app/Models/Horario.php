<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Horario extends Model
{
    protected $table = 'horarios';
    public $timestamps = false;
    protected $fillable = ['hora_inicio', 'hora_fim'];

    public function reservas(): BelongsToMany
    {
        return $this->belongsToMany(Reserva::class, 'reserva_horario', 'horario_id', 'reserva_id');
    }

    public function getIntervaloAttribute(): string
    {
        return substr($this->hora_inicio, 0, 5) . ' - ' . substr($this->hora_fim, 0, 5);
    }
}
