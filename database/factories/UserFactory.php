<?php

namespace Database\Factories;

use App\Enums\TipoUtilizador;
use App\Models\Utilizador;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Utilizador>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected $model = Utilizador::class;
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'verificado_em' => now(),
            'password_hash' => static::$password ??= Hash::make('password'),
            'tipo_user' => TipoUtilizador::Estudante,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verificado_em' => null,
        ]);
    }
}
