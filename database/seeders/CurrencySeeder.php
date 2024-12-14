<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;


class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::insert([
            [
                'name' => 'RSD',
                'date' => '2024-12-14',
                'exchange_rate' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'USD',
                'date' => '2024-12-14',
                'exchange_rate' => 112.08,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'EUR',
                'date' => '2024-12-14',
                'exchange_rate' => 117.3, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'GBP',
                'date' => '2024-12-14',
                'exchange_rate' => 141.9, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
