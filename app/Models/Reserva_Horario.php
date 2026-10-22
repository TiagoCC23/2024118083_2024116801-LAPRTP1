<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva_Horario extends Model
{
    protected $table = 'horario_reserva';
    public $timestamps = false; // -> não mexe nas datas
    protected $fillable = ['reserva_id', 'horario_id'];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class);
    }
}
