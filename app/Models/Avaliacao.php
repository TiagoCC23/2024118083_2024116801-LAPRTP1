<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    protected $fillable = ['nota', 'comentario', 'id_reserva'];

    public function reserva(){
        return $this->belongsTo(Reserva::class, 'id_reserva');
    }
}
