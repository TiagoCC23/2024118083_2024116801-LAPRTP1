<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Edificio extends Model
{
    protected $fillable = ['nome', 'siglas', 'imagem'];

    public function salas(){
        return $this->hasMany(Sala::class, 'edificio_id');
    }

    public function administradores(){
        return $this->hasMany(User::class, 'edificio_id');
    }
}
