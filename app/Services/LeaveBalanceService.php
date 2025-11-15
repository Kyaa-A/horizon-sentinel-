<?php

namespace App\Services;

use App\Models\CompanyHoliday;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveBalanceService
{
    /**
     * Calculate working days between two dates, optionally excluding holidays.
     *
     * @param  string|Carbon  $startDate
     * @param  string|Carbon  $endDate
     * @param  bool  $excludeHolidays
     * @param  string|null  $region
     * @return int
     */
    public function calculateWorkingDays(
        $startDate,
        $endDate,
        bool $excludeHolidays = true,
        ?string $region = null
    ): int {
        if ($excludeHolidays) {
            return CompanyHoliday::countWorkingDays($startDate, $endDate, $region);
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $workingDays = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if (!$current->isWeekend()) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    /**
     * Validate if a user has sufficient balance for a leave request.
     *
     * @param  int  $userId
     * @param  string  $leaveType
     * @param  float  $days
     * @param  int|null  $year
     * @return bool
     */
    public function validateBalanceSufficiency(
        int $userId,
        string $leaveType,
        float $days,
        ?int $year = null
    ): bool {
        $year = $year ?? now()->year;

        $balance = LeaveBalance::where('user_id', $userId)
            ->where('leave_type', $leaveType)
            ->where('year', $year)
            ->first();

        if (!$balance) {
            return false;
        }

        return $balance->hasSufficientBalance($days);
    }

    /**
     * Get the available balance for a user.
     *
     * @param  int  $userId
     * @param  string  $leaveType
     * @param  int|null  $year
     * @return float
     */
    public function getAvailableBalance(
        int $userId,
        string $leaveType,
        ?int $year = null
    ): float {
        $year = $year ?? now()->year;

        $balance = LeaveBalance::where('user_id', $userId)
            ->where('leave_type', $leaveType)
            ->where('year', $year)
            ->first();

        return $balance ? $balance->available : 0;
    }

    /**
     * Deduct balance when a leave request is approved.
     * Moves days from 'pending' to 'used'.
     *
     * @param  int  $leaveRequestId
     * @return void
     * @throws \Exception
     */
    public function deductFromBalance(int $leaveRequestId): void
    {
        DB::beginTransaction();

        try {
            $leaveRequest = LeaveRequest::with('user')->findOrFail($leaveRequestId);

            if ($leaveRequest->status !== 'approved') {
                throw new \Exception('Can only deduct balance from approved requests');
            }

            $balance = LeaveBalance::where('user_id', $leaveRequest->user_id)
                ->where('leave_type', $leaveRequest->leave_type)
                ->where('year', $leaveRequest->start_date->year)
                ->lockForUpdate()
                ->first();

            if (!$balance) {
                throw new \Exception('Leave balance not found for user');
            }

            // Move from pending to used
            $balance->deductBalance($leaveRequest->total_days);

            // Record history
            $balance->recordBalanceChange(
                amount: -$leaveRequest->total_days,
                changeType: 'consumption',
                performedByUserId: auth()->id() ?? $leaveRequest->manager_id,
                leaveRequestId: $leaveRequest->id,
                notes: "Leave approved: {$leaveRequest->date_range}"
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Restore balance when a leave request is denied or cancelled.
     * Moves days from 'pending' back to 'available'.
     *
     * @param  int  $leaveRequestId
     * @return void
     * @throws \Exception
     */
    public function restoreToBalance(int $leaveRequestId): void
    {
        DB::beginTransaction();

        try {
            $leaveRequest = LeaveRequest::with('user')->findOrFail($leaveRequestId);

            if (!in_array($leaveRequest->status, ['denied', 'cancelled'])) {
                throw new \Exception('Can only restore balance from denied or cancelled requests');
            }

            $balance = LeaveBalance::where('user_id', $leaveRequest->user_id)
                ->where('leave_type', $leaveRequest->leave_type)
                ->where('year', $leaveRequest->start_date->year)
                ->lockForUpdate()
                ->first();

            if (!$balance) {
                throw new \Exception('Leave balance not found for user');
            }

            // Restore from pending to available
            $balance->restoreBalance($leaveRequest->total_days);

            // Record history
            $balance->recordBalanceChange(
                amount: $leaveRequest->total_days,
                changeType: 'adjustment',
                performedByUserId: auth()->id() ?? $leaveRequest->manager_id,
                leaveRequestId: $leaveRequest->id,
                notes: "Leave {$leaveRequest->status}: balance restored"
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reserve balance when a leave request is submitted (pending approval).
     * Moves days from 'available' to 'pending'.
     *
     * @param  int  $leaveRequestId
     * @return void
     * @throws \Exception
     */
    public function reserveBalance(int $leaveRequestId): void
    {
        DB::beginTransaction();

        try {
            $leaveRequest = LeaveRequest::with('user')->findOrFail($leaveRequestId);

            if ($leaveRequest->status !== 'pending') {
                throw new \Exception('Can only reserve balance for pending requests');
            }

            $balance = LeaveBalance::where('user_id', $leaveRequest->user_id)
                ->where('leave_type', $leaveRequest->leave_type)
                ->where('year', $leaveRequest->start_date->year)
                ->lockForUpdate()
                ->first();

            if (!$balance) {
                throw new \Exception('Leave balance not found for user');
            }

            if (!$balance->hasSufficientBalance($leaveRequest->total_days)) {
                throw new \Exception('Insufficient leave balance available');
            }

            // Move from available to pending
            $balance->reserveBalance($leaveRequest->total_days);

            // Record history (no change to total, just reservation)
            $balance->recordBalanceChange(
                amount: 0,
                changeType: 'adjustment',
                performedByUserId: $leaveRequest->user_id,
                leaveRequestId: $leaveRequest->id,
                notes: "Leave requested: {$leaveRequest->total_days} days reserved"
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Initialize leave balances for a user for a specific year.
     *
     * @param  int  $userId
     * @param  int  $year
     * @param  array  $allocations  [leave_type => days]
     * @return void
     */
    public function initializeBalances(int $userId, int $year, array $allocations): void
    {
        DB::beginTransaction();

        try {
            foreach ($allocations as $leaveType => $days) {
                $balance = LeaveBalance::firstOrCreate(
                    [
                        'user_id' => $userId,
                        'leave_type' => $leaveType,
                        'year' => $year,
                    ],
                    [
                        'total_allocated' => $days,
                        'used' => 0,
                        'pending' => 0,
                        'available' => $days,
                    ]
                );

                if ($balance->wasRecentlyCreated) {
                    $balance->recordBalanceChange(
                        amount: $days,
                        changeType: 'accrual',
                        performedByUserId: auth()->id() ?? 1,
                        notes: "Initial balance allocation for {$year}"
                    );
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Manually adjust a user's leave balance (HR admin action).
     *
     * @param  int  $userId
     * @param  string  $leaveType
     * @param  float  $adjustmentAmount
     * @param  string  $notes
     * @param  int|null  $year
     * @return void
     * @throws \Exception
     */
    public function adjustBalance(
        int $userId,
        string $leaveType,
        float $adjustmentAmount,
        string $notes,
        ?int $year = null
    ): void {
        $year = $year ?? now()->year;

        DB::beginTransaction();

        try {
            $balance = LeaveBalance::where('user_id', $userId)
                ->where('leave_type', $leaveType)
                ->where('year', $year)
                ->lockForUpdate()
                ->firstOrFail();

            $balance->total_allocated += $adjustmentAmount;
            $balance->updateAvailable();

            $balance->recordBalanceChange(
                amount: $adjustmentAmount,
                changeType: 'adjustment',
                performedByUserId: auth()->id(),
                notes: $notes
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get all balances for a user for a specific year.
     *
     * @param  int  $userId
     * @param  int|null  $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserBalances(int $userId, ?int $year = null)
    {
        $year = $year ?? now()->year;

        return LeaveBalance::where('user_id', $userId)
            ->where('year', $year)
            ->get();
    }
}
