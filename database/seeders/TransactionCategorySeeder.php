<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TransactionCategory;

class TransactionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TransactionCategory::insert([
            [
                'type' => 'Food and Dining',
                'description' => 'Restaurants, Cafes, Takeout, Fast Food',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Utilities ',
                'description' => 'Electricity, Gas, Water, Internet, Phone Bills',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Groceries',
                'description' => 'Supermarkets, Farmers Market, Organic Food Stores',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Transportation',
                'description' => 'Gasoline, Public Transport, Taxi, Ride Sharing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Health and Wellness',
                'description' => 'Medical Bills, Gym Memberships, Pharmacies, Health Products',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Entertainment',
                'description' => 'Movies, Concerts, Streaming Services, Amusement Parks',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'Shopping',
                'description' => 'Clothing, Accessories, Electronics, Home Supplies',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
