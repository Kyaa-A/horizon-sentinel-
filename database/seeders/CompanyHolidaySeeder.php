<?php

namespace Database\Seeders;

use App\Models\CompanyHoliday;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CompanyHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = now()->year;
        $nextYear = $currentYear + 1;

        // US Federal Holidays for current and next year
        $holidays = [
            // Current Year
            [
                'name' => 'New Year\'s Day',
                'date' => Carbon::create($currentYear, 1, 1)->format('Y-m-d'),
                'is_recurring' => true,
            ],
            [
                'name' => 'Martin Luther King Jr. Day',
                'date' => Carbon::create($currentYear, 1, 15)->format('Y-m-d'),
                'is_recurring' => true,
            ],
            [
                'name' => 'Presidents\' Day',
                'date' => Carbon::create($currentYear, 2, 19)->format('Y-m-d'),
                'is_recurring' => false,
            ],
            [
                'name' => 'Memorial Day',
                'date' => Carbon::create($currentYear, 5, 27)->format('Y-m-d'),
                'is_recurring' => false,
            ],
            [
                'name' => 'Independence Day',
                'date' => Carbon::create($currentYear, 7, 4)->format('Y-m-d'),
                'is_recurring' => true,
            ],
            [
                'name' => 'Labor Day',
                'date' => Carbon::create($currentYear, 9, 2)->format('Y-m-d'),
                'is_recurring' => false,
            ],
            [
                'name' => 'Thanksgiving Day',
                'date' => Carbon::create($currentYear, 11, 28)->format('Y-m-d'),
                'is_recurring' => false,
            ],
            [
                'name' => 'Christmas Day',
                'date' => Carbon::create($currentYear, 12, 25)->format('Y-m-d'),
                'is_recurring' => true,
            ],

            // Next Year
            [
                'name' => 'New Year\'s Day',
                'date' => Carbon::create($nextYear, 1, 1)->format('Y-m-d'),
                'is_recurring' => true,
            ],
            [
                'name' => 'Martin Luther King Jr. Day',
                'date' => Carbon::create($nextYear, 1, 20)->format('Y-m-d'),
                'is_recurring' => true,
            ],
            [
                'name' => 'Presidents\' Day',
                'date' => Carbon::create($nextYear, 2, 17)->format('Y-m-d'),
                'is_recurring' => false,
            ],
            [
                'name' => 'Memorial Day',
                'date' => Carbon::create($nextYear, 5, 26)->format('Y-m-d'),
                'is_recurring' => false,
            ],
            [
                'name' => 'Independence Day',
                'date' => Carbon::create($nextYear, 7, 4)->format('Y-m-d'),
                'is_recurring' => true,
            ],
            [
                'name' => 'Labor Day',
                'date' => Carbon::create($nextYear, 9, 1)->format('Y-m-d'),
                'is_recurring' => false,
            ],
            [
                'name' => 'Thanksgiving Day',
                'date' => Carbon::create($nextYear, 11, 27)->format('Y-m-d'),
                'is_recurring' => false,
            ],
            [
                'name' => 'Christmas Day',
                'date' => Carbon::create($nextYear, 12, 25)->format('Y-m-d'),
                'is_recurring' => true,
            ],
        ];

        foreach ($holidays as $holiday) {
            CompanyHoliday::create($holiday);
        }

        $this->command->info('Created ' . count($holidays) . ' company holidays');
        $this->command->info('- ' . (count($holidays) / 2) . ' holidays for ' . $currentYear);
        $this->command->info('- ' . (count($holidays) / 2) . ' holidays for ' . $nextYear);
    }
}
