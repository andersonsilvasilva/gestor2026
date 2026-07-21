<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'rh',
            'permissions' => json_encode(['rh']),
            'ativo' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'admin', 'permissions' => json_encode(['admin'])]);
    }

    public function rt(): static
    {
        return $this->state(fn () => ['role' => 'rt', 'permissions' => json_encode(['rt'])]);
    }

    public function sec(): static
    {
        return $this->state(fn () => ['role' => 'sec', 'permissions' => json_encode(['sec'])]);
    }

    public function rh(): static
    {
        return $this->state(fn () => ['role' => 'rh', 'permissions' => json_encode(['rh'])]);
    }

    public function inativo(): static
    {
        return $this->state(fn () => ['ativo' => false]);
    }
}
