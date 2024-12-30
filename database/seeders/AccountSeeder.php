<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\User;
use App\Models\Currency;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $users = User::all();

        $rsdCurrency = Currency::where('name','RSD')->inRandomOrder()->first();

        foreach ($users as $user) {

            $randomCurrency = Currency::where('name', '!=', 'RSD')->inRandomOrder()->first();

            Account::create([
                'owner_id' => $user->id,
                'currency_id' => $rsdCurrency->id,
                'account_number' => 'RSD' . rand(1000000000, 9999999999),
                'type' => 'rsd account',
                'balance' => rand(1000, 10000),
            ]);
            
            Account::create([
                'owner_id' => $user->id,
                'currency_id' => $randomCurrency->id,
                'account_number' => $randomCurrency->name . rand(1000000000, 9999999999),
                'type' => 'foreign exchange',
                'balance' => rand(1000, 10000),
            ]);
        }
    }
}
