<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $fillable = ['data', 'quantidade_pessoas', 'estado', 'tipo_de_uso', 'sala_id', 'utilizador_id'];

    public function sala(){
        return $this->belongsTo(Sala::class, 'sala_id');
    }

    public function utilizador(){
        return $this->belongsTo(User::class, 'utilizador_id');
    }

    public function horarios(){
        return $this->belongsToMany(Horario::class, 'reserva_horario', 'reserva_id', 'horario_id');
    }

    public function avaliacao(){
        return $this->hasOne(Avaliacao::class, 'id_reserva');
    }
}
