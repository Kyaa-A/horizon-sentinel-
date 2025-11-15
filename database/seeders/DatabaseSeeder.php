<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CompanyHolidaySeeder::class,  // First: holidays needed for working day calculations
            UserSeeder::class,             // Second: users needed for everything else
            LeaveBalanceSeeder::class,     // Third: balances for leave requests
            LeaveRequestSeeder::class,     // Last: leave requests depend on all above
        ]);
    }
}
