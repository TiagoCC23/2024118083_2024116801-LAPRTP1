<?php

namespace App\Models;

use App\Models\Concerns\Atualizacoes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes, Atualizacoes;

    protected $table = 'utilizadores';
    protected $fillable = ['nome', 'email', 'verificado_em', 'password_hash', 'tipo_user', 'edificio_id'];
    protected $hidden = ['password_hash'];

    protected function casts(): array
    {
        return [
            'verificado_em' => 'datetime', // -> coloca a data e hora
            'password_hash' => 'hashed', // -> faz hash automaticamente
        ];
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    public function getRememberTokenName()
    {
        return '';
    }

    public function isAdmin(): bool
    {
        return $this->tipo_user === 'admin';
    }

    public function edificio(): BelongsTo
    {
        return $this->belongsTo(Edificio::class);
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    public function salasAdministradas(): BelongsToMany
    {
        return $this->belongsToMany(Sala::class, 'administra', 'utilizador_id', 'sala_id');
    }
}
