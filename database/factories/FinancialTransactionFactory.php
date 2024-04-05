<?php

namespace Database\Factories;

use App\Models\FinancialService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FinancialTransaction>
 */
class FinancialTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "date" => $this->faker->date(),
            "description" => $this->faker->sentence(),
            "debit" => $this->faker->randomFloat(2, 0, 1000), // Gera um número aleatório com até 2 casas decimais entre 0 e 1000
            "credit" => $this->faker->randomFloat(2, 0, 1000), // Gera um número aleatório com até 2 casas decimais entre 0 e 1000
            "note" => $this->faker->sentence(),
            'financial_service_id' => function () {
                return FinancialService::factory()->create()->id;
            },
        ];
    }

    protected function withFaker()
    {
        return \Faker\Factory::create('pt_BR');
    }
}
