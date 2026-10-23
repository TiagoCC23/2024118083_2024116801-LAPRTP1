<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avaliacao extends Model
{
    protected $table = 'avaliacoes';
    public $timestamps = false;
    protected $fillable = ['nota', 'comentario', 'reserva_id'];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }
}
