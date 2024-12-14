<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //ne moze kreirati povovlje pa se zato koristi seeder a kasnije potencijalno i neki api
        return [
            'name' => $this->faker->currencyCode(), 
            'date' => $this->faker->date(), 
            'exchange_rate' => $this->faker->randomFloat(4, 0.5, 2),
        ];
    }
}
