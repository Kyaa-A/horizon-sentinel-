<?php

namespace Database\Seeders;

use App\Models\CompanyHoliday;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LeaveRequestSeeder extends Seeder
{
    /**
     * Calculate working days between two dates.
     */
    private function calculateWorkingDays(string $startDate, string $endDate): int
    {
        return CompanyHoliday::countWorkingDays($startDate, $endDate);
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get managers and employees
        $managers = User::where('role', 'manager')->get();
        $employees = User::where('role', 'employee')->get();

        if ($managers->isEmpty() || $employees->isEmpty()) {
            $this->command->warn('No managers or employees found. Please run UserSeeder first.');

            return;
        }

        $manager1 = $managers->first();
        $manager2 = $managers->last();

        // Get employees for each manager
        $manager1Employees = $employees->where('manager_id', $manager1->id);
        $manager2Employees = $employees->where('manager_id', $manager2->id);

        // Create various leave requests for testing

        // 1. PENDING REQUESTS
        if ($employee1 = $manager1Employees->first()) {
            $startDate = Carbon::now()->addDays(10)->format('Y-m-d');
            $endDate = Carbon::now()->addDays(14)->format('Y-m-d');
            $request = LeaveRequest::create([
                'user_id' => $employee1->id,
                'manager_id' => $employee1->manager_id,
                'leave_type' => 'vacation',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_days' => $this->calculateWorkingDays($startDate, $endDate),
                'status' => 'pending',
                'employee_notes' => 'Planning a family vacation to the beach.',
                'submitted_at' => Carbon::now()->subDays(2),
            ]);
            $request->recordHistory('submitted', $employee1->id, 'Initial submission');
        }

        if ($employee2 = $manager1Employees->skip(1)->first()) {
            $startDate = Carbon::now()->addDays(5)->format('Y-m-d');
            $endDate = Carbon::now()->addDays(7)->format('Y-m-d');
            $request = LeaveRequest::create([
                'user_id' => $employee2->id,
                'manager_id' => $employee2->manager_id,
                'leave_type' => 'sick_leave',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_days' => $this->calculateWorkingDays($startDate, $endDate),
                'status' => 'pending',
                'employee_notes' => 'Medical appointment scheduled.',
                'submitted_at' => Carbon::now()->subDays(1),
            ]);
            $request->recordHistory('submitted', $employee2->id, 'Initial submission');
        }

        if ($employee3 = $manager2Employees->first()) {
            $startDate = Carbon::now()->addDays(20)->format('Y-m-d');
            $endDate = Carbon::now()->addDays(25)->format('Y-m-d');
            $request = LeaveRequest::create([
                'user_id' => $employee3->id,
                'manager_id' => $employee3->manager_id,
                'leave_type' => 'paid_time_off',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_days' => $this->calculateWorkingDays($startDate, $endDate),
                'status' => 'pending',
                'employee_notes' => 'Need some time to recharge.',
                'submitted_at' => Carbon::now(),
            ]);
            $request->recordHistory('submitted', $employee3->id, 'Initial submission');
        }

        // 2. APPROVED REQUESTS (some overlapping for conflict detection)
        if ($employee4 = $manager1Employees->skip(2)->first()) {
            $request = LeaveRequest::create([
                'user_id' => $employee4->id,
                'manager_id' => $employee4->manager_id,
                'leave_type' => 'vacation',
                'start_date' => Carbon::now()->addDays(15)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(20)->format('Y-m-d'),
                'status' => 'approved',
                'employee_notes' => 'Anniversary trip.',
                'manager_notes' => 'Approved. Enjoy your trip!',
                'submitted_at' => Carbon::now()->subDays(10),
                'reviewed_at' => Carbon::now()->subDays(9),
            ]);
            $request->recordHistory('submitted', $employee4->id, 'Initial submission');
            $request->recordHistory('approved', $manager1->id, 'Approved by manager');
        }

        // Overlapping request (same dates as another pending request)
        if ($employee5 = $manager1Employees->skip(3)->first()) {
            $request = LeaveRequest::create([
                'user_id' => $employee5->id,
                'manager_id' => $employee5->manager_id,
                'leave_type' => 'vacation',
                'start_date' => Carbon::now()->addDays(12)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(16)->format('Y-m-d'),
                'status' => 'approved',
                'employee_notes' => 'Conference attendance.',
                'manager_notes' => 'Approved for professional development.',
                'submitted_at' => Carbon::now()->subDays(15),
                'reviewed_at' => Carbon::now()->subDays(14),
            ]);
            $request->recordHistory('submitted', $employee5->id, 'Initial submission');
            $request->recordHistory('approved', $manager1->id, 'Approved - professional development');
        }

        if ($employee6 = $manager2Employees->skip(1)->first()) {
            $request = LeaveRequest::create([
                'user_id' => $employee6->id,
                'manager_id' => $employee6->manager_id,
                'leave_type' => 'paid_time_off',
                'start_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(35)->format('Y-m-d'),
                'status' => 'approved',
                'employee_notes' => 'Summer vacation.',
                'manager_notes' => 'Approved. Have a great time!',
                'submitted_at' => Carbon::now()->subDays(20),
                'reviewed_at' => Carbon::now()->subDays(18),
            ]);
            $request->recordHistory('submitted', $employee6->id, 'Initial submission');
            $request->recordHistory('approved', $manager2->id, 'Approved');
        }

        // 3. DENIED REQUESTS
        if ($employee7 = $manager2Employees->skip(2)->first()) {
            $request = LeaveRequest::create([
                'user_id' => $employee7->id,
                'manager_id' => $employee7->manager_id,
                'leave_type' => 'unpaid_leave',
                'start_date' => Carbon::now()->addDays(8)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(12)->format('Y-m-d'),
                'status' => 'denied',
                'employee_notes' => 'Personal matters.',
                'manager_notes' => 'Too many team members already on leave during this period.',
                'submitted_at' => Carbon::now()->subDays(5),
                'reviewed_at' => Carbon::now()->subDays(4),
            ]);
            $request->recordHistory('submitted', $employee7->id, 'Initial submission');
            $request->recordHistory('denied', $manager2->id, 'Denied - staffing conflicts');
        }

        // 4. HISTORICAL REQUESTS (past dates)
        if ($employee8 = $manager1Employees->skip(4)->first()) {
            $request = LeaveRequest::create([
                'user_id' => $employee8->id,
                'manager_id' => $employee8->manager_id,
                'leave_type' => 'sick_leave',
                'start_date' => Carbon::now()->subDays(30)->format('Y-m-d'),
                'end_date' => Carbon::now()->subDays(28)->format('Y-m-d'),
                'status' => 'approved',
                'employee_notes' => 'Flu symptoms.',
                'manager_notes' => 'Approved. Hope you feel better.',
                'submitted_at' => Carbon::now()->subDays(31),
                'reviewed_at' => Carbon::now()->subDays(30),
            ]);
            $request->recordHistory('submitted', $employee8->id, 'Initial submission');
            $request->recordHistory('approved', $manager1->id, 'Approved');
        }

        if ($employee9 = $manager2Employees->skip(3)->first()) {
            $request = LeaveRequest::create([
                'user_id' => $employee9->id,
                'manager_id' => $employee9->manager_id,
                'leave_type' => 'vacation',
                'start_date' => Carbon::now()->subDays(60)->format('Y-m-d'),
                'end_date' => Carbon::now()->subDays(53)->format('Y-m-d'),
                'status' => 'approved',
                'employee_notes' => 'Holiday trip.',
                'manager_notes' => 'Approved.',
                'submitted_at' => Carbon::now()->subDays(75),
                'reviewed_at' => Carbon::now()->subDays(70),
            ]);
            $request->recordHistory('submitted', $employee9->id, 'Initial submission');
            $request->recordHistory('approved', $manager2->id, 'Approved');
        }

        // 5. CANCELLED REQUEST
        if ($employee10 = $manager2Employees->skip(4)->first()) {
            $request = LeaveRequest::create([
                'user_id' => $employee10->id,
                'manager_id' => $employee10->manager_id,
                'leave_type' => 'vacation',
                'start_date' => Carbon::now()->addDays(40)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(45)->format('Y-m-d'),
                'status' => 'cancelled',
                'employee_notes' => 'Change of plans.',
                'manager_notes' => 'Originally approved.',
                'submitted_at' => Carbon::now()->subDays(25),
                'reviewed_at' => Carbon::now()->subDays(23),
            ]);
            $request->recordHistory('submitted', $employee10->id, 'Initial submission');
            $request->recordHistory('approved', $manager2->id, 'Approved');
            $request->recordHistory('cancelled', $employee10->id, 'Plans changed - request cancelled');
        }

        $this->command->info('Leave requests and history created successfully!');
    }
}
