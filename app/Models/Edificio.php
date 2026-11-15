<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Edificio extends Model
{
    protected $table = 'edificios';
    public $timestamps = false; // -> não mexe nas datas
    protected $fillable = ['nome', 'sigla', 'imagem'];

    public function salas(): HasMany
    {
        return $this->hasMany(Sala::class);
    }

    public function utilizadores(): HasMany
    {
        return $this->hasMany(Utilizador::class);
    }
}
