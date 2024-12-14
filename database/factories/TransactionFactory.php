<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Account;
use App\Models\TransactionCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $accounts = Account::all();
        $categories = TransactionCategory::all();

        
        $sender = $accounts->random();
        $receiver = $accounts->random();

        while ($sender->id === $receiver->id || $sender->currency_id === $receiver->currency_id) {
            $receiver = $accounts->random();
        }

        return [
            'sender_account' => $sender->id,  
            'receiver_account' => $receiver->id,  
            'date' => $this->faker->dateTimeBetween('-30 days', 'now'), 
            'amount' => $this->faker->randomFloat(2, 1, $sender->balance),
            'description' => $this->faker->sentence(5),
            'status' => $this->faker->randomElement(['successful', 'failed']), 
            'category_id' => $categories->random()->id,  
        ];
    }
}
