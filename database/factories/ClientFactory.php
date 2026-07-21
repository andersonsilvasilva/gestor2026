<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cns'             => fake()->unique()->numerify('####################'),
            'nome'            => fake()->name(),
            'sexo'            => fake()->randomElement(['Masculino', 'Feminino']),
            'data_nascimento' => fake()->date(),
            'nacionalidade'   => 'Brasileira',
            'cpf'             => fake()->unique()->numerify('###########'),
            'estado_civil'    => 'Solteiro',
            'cor'             => 'Parda',
            'cep'             => fake()->numerify('#####-###'),
            'logradouro'      => 'Rua',
            'endereco'        => fake()->streetName(),
            'numero'          => (string) fake()->buildingNumber(),
            'bairro'          => fake()->word(),
            'cidade'          => 'Cuiabá',
            'uf'              => 'MT',
            'telefone'        => fake()->numerify('(##) #####-####'),
            'email'           => fake()->unique()->safeEmail(),
            'tipo_sangue'     => 'O+',
            'create_id'       => now(),
            'update_id'       => now(),
            'ativo'           => '1',
        ];
    }
}
