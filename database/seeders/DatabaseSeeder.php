<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            //UserSeeder::class,
            //CurrencySeeder::class,
            //AccountSeeder::class,
            //TransactionCategorySeeder::class,
            //TransactionSeeder::class
           //EmployeeSeeder::class
        ]);
    }
}
