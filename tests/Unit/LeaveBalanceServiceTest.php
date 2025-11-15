<?php

namespace Tests\Unit;

use App\Models\CompanyHoliday;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\LeaveBalanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveBalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LeaveBalanceService $service;
    protected User $user;
    protected User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new LeaveBalanceService();

        $this->manager = User::factory()->create(['role' => 'manager']);
        $this->user = User::factory()->create([
            'role' => 'employee',
            'manager_id' => $this->manager->id,
        ]);
    }

    /** @test */
    public function it_calculates_working_days_excluding_weekends()
    {
        // Monday to Friday (5 working days)
        $startDate = '2025-11-17'; // Monday
        $endDate = '2025-11-21';   // Friday

        $workingDays = $this->service->calculateWorkingDays($startDate, $endDate, false);

        $this->assertEquals(5, $workingDays);
    }

    /** @test */
    public function it_calculates_working_days_excluding_weekends_and_holidays()
    {
        // Create a holiday on Wednesday
        CompanyHoliday::create([
            'name' => 'Test Holiday',
            'date' => '2025-11-19', // Wednesday
            'is_recurring' => false,
        ]);

        // Monday to Friday, minus Wednesday holiday = 4 working days
        $startDate = '2025-11-17';
        $endDate = '2025-11-21';

        $workingDays = $this->service->calculateWorkingDays($startDate, $endDate, true);

        $this->assertEquals(4, $workingDays);
    }

    /** @test */
    public function it_validates_sufficient_balance()
    {
        LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 5,
            'pending' => 2,
            'available' => 8,
            'year' => 2025,
        ]);

        $hasSufficient = $this->service->validateBalanceSufficiency(
            $this->user->id,
            'paid_time_off',
            5
        );

        $this->assertTrue($hasSufficient);
    }

    /** @test */
    public function it_validates_insufficient_balance()
    {
        LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 5,
            'pending' => 2,
            'available' => 8,
            'year' => 2025,
        ]);

        $hasSufficient = $this->service->validateBalanceSufficiency(
            $this->user->id,
            'paid_time_off',
            10
        );

        $this->assertFalse($hasSufficient);
    }

    /** @test */
    public function it_returns_false_when_balance_does_not_exist()
    {
        $hasSufficient = $this->service->validateBalanceSufficiency(
            $this->user->id,
            'paid_time_off',
            5
        );

        $this->assertFalse($hasSufficient);
    }

    /** @test */
    public function it_gets_available_balance()
    {
        LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 5,
            'pending' => 2,
            'available' => 8,
            'year' => 2025,
        ]);

        $available = $this->service->getAvailableBalance(
            $this->user->id,
            'paid_time_off'
        );

        $this->assertEquals(8, $available);
    }

    /** @test */
    public function it_returns_zero_when_balance_does_not_exist()
    {
        $available = $this->service->getAvailableBalance(
            $this->user->id,
            'paid_time_off'
        );

        $this->assertEquals(0, $available);
    }

    /** @test */
    public function it_reserves_balance_for_pending_request()
    {
        $balance = LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 5,
            'pending' => 0,
            'available' => 10,
            'year' => 2025,
        ]);

        $leaveRequest = LeaveRequest::create([
            'user_id' => $this->user->id,
            'manager_id' => $this->manager->id,
            'leave_type' => 'paid_time_off',
            'start_date' => '2025-11-17',
            'end_date' => '2025-11-21',
            'total_days' => 5,
            'status' => 'pending',
        ]);

        $this->service->reserveBalance($leaveRequest->id);

        $balance->refresh();

        $this->assertEquals(5, $balance->pending);
        $this->assertEquals(5, $balance->available);
        $this->assertEquals(5, $balance->used);
    }

    /** @test */
    public function it_throws_exception_when_reserving_with_insufficient_balance()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient leave balance available');

        $balance = LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 12,
            'pending' => 0,
            'available' => 3,
            'year' => 2025,
        ]);

        $leaveRequest = LeaveRequest::create([
            'user_id' => $this->user->id,
            'manager_id' => $this->manager->id,
            'leave_type' => 'paid_time_off',
            'start_date' => '2025-11-17',
            'end_date' => '2025-11-21',
            'total_days' => 5,
            'status' => 'pending',
        ]);

        $this->service->reserveBalance($leaveRequest->id);
    }

    /** @test */
    public function it_deducts_balance_when_request_is_approved()
    {
        $balance = LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 5,
            'pending' => 5,
            'available' => 5,
            'year' => 2025,
        ]);

        $leaveRequest = LeaveRequest::create([
            'user_id' => $this->user->id,
            'manager_id' => $this->manager->id,
            'leave_type' => 'paid_time_off',
            'start_date' => '2025-11-17',
            'end_date' => '2025-11-21',
            'total_days' => 5,
            'status' => 'approved',
        ]);

        $this->actingAs($this->manager);
        $this->service->deductFromBalance($leaveRequest->id);

        $balance->refresh();

        $this->assertEquals(10, $balance->used);
        $this->assertEquals(0, $balance->pending);
        $this->assertEquals(5, $balance->available);
    }

    /** @test */
    public function it_throws_exception_when_deducting_from_non_approved_request()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can only deduct balance from approved requests');

        LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 5,
            'pending' => 5,
            'available' => 5,
            'year' => 2025,
        ]);

        $leaveRequest = LeaveRequest::create([
            'user_id' => $this->user->id,
            'manager_id' => $this->manager->id,
            'leave_type' => 'paid_time_off',
            'start_date' => '2025-11-17',
            'end_date' => '2025-11-21',
            'total_days' => 5,
            'status' => 'pending',
        ]);

        $this->service->deductFromBalance($leaveRequest->id);
    }

    /** @test */
    public function it_restores_balance_when_request_is_denied()
    {
        $balance = LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 5,
            'pending' => 5,
            'available' => 5,
            'year' => 2025,
        ]);

        $leaveRequest = LeaveRequest::create([
            'user_id' => $this->user->id,
            'manager_id' => $this->manager->id,
            'leave_type' => 'paid_time_off',
            'start_date' => '2025-11-17',
            'end_date' => '2025-11-21',
            'total_days' => 5,
            'status' => 'denied',
        ]);

        $this->actingAs($this->manager);
        $this->service->restoreToBalance($leaveRequest->id);

        $balance->refresh();

        $this->assertEquals(5, $balance->used);
        $this->assertEquals(0, $balance->pending);
        $this->assertEquals(10, $balance->available);
    }

    /** @test */
    public function it_restores_balance_when_request_is_cancelled()
    {
        $balance = LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 5,
            'pending' => 5,
            'available' => 5,
            'year' => 2025,
        ]);

        $leaveRequest = LeaveRequest::create([
            'user_id' => $this->user->id,
            'manager_id' => $this->manager->id,
            'leave_type' => 'paid_time_off',
            'start_date' => '2025-11-17',
            'end_date' => '2025-11-21',
            'total_days' => 5,
            'status' => 'cancelled',
        ]);

        $this->actingAs($this->user);
        $this->service->restoreToBalance($leaveRequest->id);

        $balance->refresh();

        $this->assertEquals(5, $balance->used);
        $this->assertEquals(0, $balance->pending);
        $this->assertEquals(10, $balance->available);
    }

    /** @test */
    public function it_throws_exception_when_restoring_from_invalid_status()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can only restore balance from denied or cancelled requests');

        LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 5,
            'pending' => 5,
            'available' => 5,
            'year' => 2025,
        ]);

        $leaveRequest = LeaveRequest::create([
            'user_id' => $this->user->id,
            'manager_id' => $this->manager->id,
            'leave_type' => 'paid_time_off',
            'start_date' => '2025-11-17',
            'end_date' => '2025-11-21',
            'total_days' => 5,
            'status' => 'approved',
        ]);

        $this->service->restoreToBalance($leaveRequest->id);
    }

    /** @test */
    public function it_initializes_balances_for_user()
    {
        $allocations = [
            'paid_time_off' => 15,
            'sick_leave' => 10,
            'vacation' => 20,
        ];

        $this->actingAs($this->manager);
        $this->service->initializeBalances($this->user->id, 2025, $allocations);

        $balances = LeaveBalance::where('user_id', $this->user->id)
            ->where('year', 2025)
            ->get();

        $this->assertCount(3, $balances);

        $ptoBalance = $balances->firstWhere('leave_type', 'paid_time_off');
        $this->assertEquals(15, $ptoBalance->total_allocated);
        $this->assertEquals(15, $ptoBalance->available);
        $this->assertEquals(0, $ptoBalance->used);
        $this->assertEquals(0, $ptoBalance->pending);
    }

    /** @test */
    public function it_adjusts_balance_manually()
    {
        $balance = LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 5,
            'pending' => 0,
            'available' => 10,
            'year' => 2025,
        ]);

        $hrAdmin = User::factory()->create(['role' => 'hr_admin']);
        $this->actingAs($hrAdmin);

        $this->service->adjustBalance(
            $this->user->id,
            'paid_time_off',
            5,
            'Bonus days awarded'
        );

        $balance->refresh();

        $this->assertEquals(20, $balance->total_allocated);
        $this->assertEquals(15, $balance->available);
    }

    /** @test */
    public function it_gets_all_user_balances_for_year()
    {
        LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 5,
            'pending' => 0,
            'available' => 10,
            'year' => 2025,
        ]);

        LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'sick_leave',
            'total_allocated' => 10,
            'used' => 2,
            'pending' => 0,
            'available' => 8,
            'year' => 2025,
        ]);

        // Different year - should not be included
        LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 0,
            'pending' => 0,
            'available' => 15,
            'year' => 2024,
        ]);

        $balances = $this->service->getUserBalances($this->user->id, 2025);

        $this->assertCount(2, $balances);
    }

    /** @test */
    public function it_records_balance_history_when_reserving()
    {
        $balance = LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 5,
            'pending' => 0,
            'available' => 10,
            'year' => 2025,
        ]);

        $leaveRequest = LeaveRequest::create([
            'user_id' => $this->user->id,
            'manager_id' => $this->manager->id,
            'leave_type' => 'paid_time_off',
            'start_date' => '2025-11-17',
            'end_date' => '2025-11-21',
            'total_days' => 5,
            'status' => 'pending',
        ]);

        $this->service->reserveBalance($leaveRequest->id);

        $this->assertDatabaseHas('leave_balance_history', [
            'leave_balance_id' => $balance->id,
            'leave_request_id' => $leaveRequest->id,
            'change_type' => 'adjustment',
        ]);
    }

    /** @test */
    public function it_records_balance_history_when_deducting()
    {
        $balance = LeaveBalance::create([
            'user_id' => $this->user->id,
            'leave_type' => 'paid_time_off',
            'total_allocated' => 15,
            'used' => 5,
            'pending' => 5,
            'available' => 5,
            'year' => 2025,
        ]);

        $leaveRequest = LeaveRequest::create([
            'user_id' => $this->user->id,
            'manager_id' => $this->manager->id,
            'leave_type' => 'paid_time_off',
            'start_date' => '2025-11-17',
            'end_date' => '2025-11-21',
            'total_days' => 5,
            'status' => 'approved',
        ]);

        $this->actingAs($this->manager);
        $this->service->deductFromBalance($leaveRequest->id);

        $this->assertDatabaseHas('leave_balance_history', [
            'leave_balance_id' => $balance->id,
            'leave_request_id' => $leaveRequest->id,
            'change_type' => 'consumption',
            'change_amount' => -5,
        ]);
    }
}
