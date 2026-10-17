<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sala extends Model
{
    protected $fillable = ['nome_numero', 'capacidade_maxima', 'tipo_sala', 'piso', 'edificio_id'];

    public function edificio(){
        return $this->belongsTo(Edificio::class, 'edificio_id');
    }

    public function reservas(){
        return $this->hasMany(Reserva::class, 'sala_id');
    }
}
