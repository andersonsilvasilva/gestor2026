<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Procediment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\eventos>
 */
class EventosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'         => User::factory()->rh(),
            'client_id'       => Client::factory(),
            'procediments_id' => Procediment::factory(),
            'title'           => fake()->words(2, true),
            'start'           => fake()->dateTimeBetween('-1 month', 'now'),
            'end'             => fake()->dateTimeBetween('now', '+1 hour'),
            'color'           => '#0000FF',
            'descricao'       => fake()->sentence(),
            'status'          => '1',
        ];
    }
}
