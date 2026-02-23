<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome'      => $this->faker->name(),
            'tipo'      => $this->faker->randomElement(['pessoa_fisica', 'pessoa_juridica']),
            'email'     => $this->faker->unique()->safeEmail(),
            'telefone'  => $this->faker->phoneNumber(),
            'cpf_cnpj'  => $this->faker->unique()->numerify('###.###.###-##'),
            'logradouro' => $this->faker->streetName(),
            'numero'    => $this->faker->buildingNumber(),
            'bairro'    => $this->faker->word(),
            'cidade'    => $this->faker->city(),
            'estado'    => $this->faker->lexify('??'),
            'cep'       => $this->faker->numerify('#####-###'),
            'ativo'     => true,
        ];
    }
}
