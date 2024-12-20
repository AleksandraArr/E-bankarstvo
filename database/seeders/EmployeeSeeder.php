<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
        Employee::create([
            'email' => 'support@example.com',
            'password' => bcrypt('password'),
            'role' => 'support'
        ]);
    }
}

//admin t99gE7uIQMtK4xkVVps3e39APNwXhKlVn6dDintI9a2b90a3
//user QDRGGT5ABftDQPK8Axh8MYJWonRemUlBIqtf8FyRdfc9714b
