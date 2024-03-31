<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
        ];
    }

    protected function withFaker()
    {
        return \Faker\Factory::create('pt_BR');
    }
}
