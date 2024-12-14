<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User; 
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        //ovime se omogucava da korisnik ima vise dinarskih racuna, sto nije dozvoljeno
            return [
                'owner_id' => User::inRandomOrder()->first()->id,  
                'currency_id' => Currency::inRandomOrder()->first()->id,  
                'account_number' => $this->faker->unique()->numerify('############'),  
                'type' => $this->faker->randomElement(['rsd account', 'foreign currency']),
                'balance' => $this->faker->randomFloat(2, 1000, 10000), 
            ];
    }
}
