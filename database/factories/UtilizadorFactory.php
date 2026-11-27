<?php

namespace Database\Factories;

use App\Enums\TipoUtilizador;
use App\Models\Edificio;
use App\Models\Utilizador;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Utilizador>
 */
class UtilizadorFactory extends Factory
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
            'nome' => fake('pt_PT')->name(),
            'email' => fake()->unique()->numerify('20########') . '@ufp.edu.pt',
            'password_hash' => static::$password ??= Hash::make('password'),
            'tipo_user' => TipoUtilizador::Estudante,
            'edificio_id' => Edificio::inRandomOrder()->value('id'),
            'verificado_em' => now(),
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
