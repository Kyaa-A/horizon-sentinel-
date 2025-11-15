<?php

namespace Database\Seeders;

use App\Models\LeaveBalance;
use App\Models\User;
use App\Services\LeaveBalanceService;
use Illuminate\Database\Seeder;

class LeaveBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveBalanceService = new LeaveBalanceService();
        $currentYear = now()->year;

        // Get all employees and managers (not HR admin)
        $users = User::whereIn('role', ['employee', 'manager'])->get();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        $balanceCount = 0;

        foreach ($users as $user) {
            // Standard leave allocations
            $allocations = [
                'paid_time_off' => 15,  // 15 days PTO
                'sick_leave' => 10,     // 10 days sick leave
                'vacation' => 20,        // 20 days vacation
            ];

            // Initialize balances for current year
            $leaveBalanceService->initializeBalances($user->id, $currentYear, $allocations);

            // Create some realistic usage patterns
            if ($user->role === 'employee') {
                // Random usage for employees (0-5 days used from each type)
                $ptoBalance = $user->getLeaveBalance('paid_time_off', $currentYear);
                if ($ptoBalance) {
                    $used = rand(0, 5);
                    $ptoBalance->used = $used;
                    $ptoBalance->pending = 0;
                    $ptoBalance->updateAvailable();

                    if ($used > 0) {
                        $ptoBalance->recordBalanceChange(
                            amount: -$used,
                            changeType: 'consumption',
                            performedByUserId: $user->id,
                            notes: 'Historical usage (seeded data)'
                        );
                    }
                }

                $sickBalance = $user->getLeaveBalance('sick_leave', $currentYear);
                if ($sickBalance) {
                    $used = rand(0, 3);
                    $sickBalance->used = $used;
                    $sickBalance->pending = 0;
                    $sickBalance->updateAvailable();

                    if ($used > 0) {
                        $sickBalance->recordBalanceChange(
                            amount: -$used,
                            changeType: 'consumption',
                            performedByUserId: $user->id,
                            notes: 'Historical usage (seeded data)'
                        );
                    }
                }

                $vacationBalance = $user->getLeaveBalance('vacation', $currentYear);
                if ($vacationBalance) {
                    $used = rand(0, 8);
                    $vacationBalance->used = $used;
                    $vacationBalance->pending = 0;
                    $vacationBalance->updateAvailable();

                    if ($used > 0) {
                        $vacationBalance->recordBalanceChange(
                            amount: -$used,
                            changeType: 'consumption',
                            performedByUserId: $user->id,
                            notes: 'Historical usage (seeded data)'
                        );
                    }
                }
            }

            $balanceCount += 3; // 3 leave types per user
        }

        $this->command->info('Created ' . $balanceCount . ' leave balance records');
        $this->command->info('- ' . $users->count() . ' users with 3 leave types each');
        $this->command->info('- Balances initialized for year ' . $currentYear);
    }
}
