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
            'jmbg' => $this->faker->unique()->numerify('#############'), 
            'first_name' => $this->faker->firstName, 
            'last_name' => $this->faker->lastName,
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            //'password' => static::$password ??= Hash::make('password'),
        ];
    }
}
