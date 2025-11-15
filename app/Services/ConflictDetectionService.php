<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ConflictDetectionService
{
    /**
     * Minimum acceptable team availability percentage.
     */
    protected const MIN_AVAILABILITY_THRESHOLD = 30.0;

    /**
     * Check for conflicts for a given leave request.
     */
    public function checkConflicts(LeaveRequest $leaveRequest, int $managerId): array
    {
        $conflicts = [];

        // Check for overlapping leaves
        $overlappingLeaves = $this->getOverlappingLeaves($leaveRequest, $managerId);

        if ($overlappingLeaves->isNotEmpty()) {
            $severity = $this->calculateSeverity($overlappingLeaves->count());

            $conflicts[] = [
                'type' => 'overlap',
                'severity' => $severity,
                'message' => $overlappingLeaves->count().' team member(s) already on leave during this period',
                'details' => $overlappingLeaves->map(fn ($leave) => [
                    'employee' => $leave->user->name,
                    'dates' => $leave->start_date->format('M d').' - '.$leave->end_date->format('M d, Y'),
                    'leave_type' => $leave->leave_type,
                ]),
            ];
        }

        // Check team availability threshold
        $availabilityConflict = $this->checkAvailabilityThreshold($leaveRequest, $managerId);
        if ($availabilityConflict) {
            $conflicts[] = $availabilityConflict;
        }

        // Check for sequential leaves (same person taking back-to-back time off)
        $sequentialConflict = $this->checkSequentialLeaves($leaveRequest);
        if ($sequentialConflict) {
            $conflicts[] = $sequentialConflict;
        }

        return $conflicts;
    }

    /**
     * Get overlapping approved/pending leaves for the given request.
     */
    protected function getOverlappingLeaves(LeaveRequest $leaveRequest, int $managerId): Collection
    {
        return LeaveRequest::forManager($managerId)
            ->whereIn('status', ['approved', 'pending'])
            ->where('id', '!=', $leaveRequest->id)
            ->where('user_id', '!=', $leaveRequest->user_id) // Exclude same employee
            ->overlapping(
                $leaveRequest->start_date->format('Y-m-d'),
                $leaveRequest->end_date->format('Y-m-d')
            )
            ->with('user')
            ->get();
    }

    /**
     * Calculate conflict severity based on number of overlapping leaves.
     */
    protected function calculateSeverity(int $overlapCount): string
    {
        if ($overlapCount >= 3) {
            return 'critical';
        } elseif ($overlapCount >= 2) {
            return 'high';
        } else {
            return 'medium';
        }
    }

    /**
     * Check if approving this request would violate minimum availability threshold.
     */
    protected function checkAvailabilityThreshold(LeaveRequest $leaveRequest, int $managerId): ?array
    {
        $manager = User::find($managerId);
        $teamSize = $manager->directReports()->count();

        if ($teamSize === 0) {
            return null;
        }

        // Count how many people would be on leave
        $overlappingCount = LeaveRequest::forManager($managerId)
            ->whereIn('status', ['approved', 'pending'])
            ->where('id', '!=', $leaveRequest->id)
            ->overlapping(
                $leaveRequest->start_date->format('Y-m-d'),
                $leaveRequest->end_date->format('Y-m-d')
            )
            ->distinct('user_id')
            ->count('user_id');

        // Add 1 for the current request
        $totalOnLeave = $overlappingCount + 1;
        $availableCount = $teamSize - $totalOnLeave;
        $availabilityPercentage = ($availableCount / $teamSize) * 100;

        if ($availabilityPercentage < self::MIN_AVAILABILITY_THRESHOLD) {
            return [
                'type' => 'availability',
                'severity' => 'high',
                'message' => sprintf(
                    'Team availability would drop to %.1f%% (minimum is %.1f%%)',
                    $availabilityPercentage,
                    self::MIN_AVAILABILITY_THRESHOLD
                ),
                'details' => [
                    'team_size' => $teamSize,
                    'on_leave' => $totalOnLeave,
                    'available' => $availableCount,
                    'percentage' => round($availabilityPercentage, 1),
                ],
            ];
        }

        return null;
    }

    /**
     * Check if the employee has other leaves close to this one (sequential pattern).
     */
    protected function checkSequentialLeaves(LeaveRequest $leaveRequest): ?array
    {
        // Look for leaves within 5 days before or after this request
        $bufferDays = 5;
        $checkStartDate = $leaveRequest->start_date->copy()->subDays($bufferDays);
        $checkEndDate = $leaveRequest->end_date->copy()->addDays($bufferDays);

        $nearbyLeaves = LeaveRequest::where('user_id', $leaveRequest->user_id)
            ->where('id', '!=', $leaveRequest->id)
            ->whereIn('status', ['approved', 'pending'])
            ->where(function ($query) use ($checkStartDate, $checkEndDate) {
                $query->whereBetween('start_date', [$checkStartDate, $checkEndDate])
                    ->orWhereBetween('end_date', [$checkStartDate, $checkEndDate]);
            })
            ->get();

        if ($nearbyLeaves->isNotEmpty()) {
            return [
                'type' => 'sequential',
                'severity' => 'low',
                'message' => 'Employee has other leave requests close to this period',
                'details' => $nearbyLeaves->map(fn ($leave) => [
                    'dates' => $leave->start_date->format('M d').' - '.$leave->end_date->format('M d, Y'),
                    'status' => $leave->status,
                ]),
            ];
        }

        return null;
    }

    /**
     * Calculate team availability for a given date range.
     * Returns average daily availability across the period.
     */
    public function calculateTeamAvailability(int $managerId, Carbon $startDate, Carbon $endDate): array
    {
        $manager = User::find($managerId);
        $teamSize = $manager->directReports()->count();

        if ($teamSize === 0) {
            return [
                'team_size' => 0,
                'available' => 0,
                'on_leave' => 0,
                'percentage' => 0,
            ];
        }

        // Calculate average daily availability
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $totalPersonDays = $teamSize * $totalDays;

        // Get all leaves in the period
        $leaves = LeaveRequest::forManager($managerId)
            ->whereIn('status', ['approved', 'pending'])
            ->overlapping($startDate->format('Y-m-d'), $endDate->format('Y-m-d'))
            ->get();

        // Count total leave days
        $totalLeaveDays = 0;
        foreach ($leaves as $leave) {
            $leaveStart = max($leave->start_date, $startDate);
            $leaveEnd = min($leave->end_date, $endDate);
            $totalLeaveDays += $leaveStart->diffInDays($leaveEnd) + 1;
        }

        $availablePersonDays = $totalPersonDays - $totalLeaveDays;
        $averageAvailable = $availablePersonDays / $totalDays;
        $percentage = ($averageAvailable / $teamSize) * 100;

        // Count unique people with leave for display
        $uniqueOnLeave = $leaves->unique('user_id')->count();

        return [
            'team_size' => $teamSize,
            'available' => round($averageAvailable, 1),
            'on_leave' => $uniqueOnLeave,
            'percentage' => round($percentage, 1),
        ];
    }

    /**
     * Get daily availability breakdown for a month.
     */
    public function getDailyAvailability(int $managerId, Carbon $month): array
    {
        $manager = User::find($managerId);
        $teamSize = $manager->directReports()->count();

        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();
        $currentDate = $startDate->copy();

        $dailyData = [];

        while ($currentDate <= $endDate) {
            $onLeaveCount = LeaveRequest::forManager($managerId)
                ->approved()
                ->where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->distinct('user_id')
                ->count('user_id');

            $availableCount = $teamSize - $onLeaveCount;
            $percentage = $teamSize > 0 ? ($availableCount / $teamSize) * 100 : 0;

            $dailyData[$currentDate->format('Y-m-d')] = [
                'date' => $currentDate->format('Y-m-d'),
                'available' => $availableCount,
                'on_leave' => $onLeaveCount,
                'percentage' => round($percentage, 1),
                'severity' => $this->getAvailabilitySeverity($percentage),
            ];

            $currentDate->addDay();
        }

        return $dailyData;
    }

    /**
     * Get severity level based on availability percentage.
     */
    protected function getAvailabilitySeverity(float $percentage): string
    {
        if ($percentage < self::MIN_AVAILABILITY_THRESHOLD) {
            return 'critical';
        } elseif ($percentage < 50) {
            return 'high';
        } elseif ($percentage < 70) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Get conflict summary for a manager's team.
     */
    public function getConflictSummary(int $managerId): array
    {
        $pendingRequests = LeaveRequest::forManager($managerId)
            ->pending()
            ->get();

        $totalConflicts = 0;
        $criticalConflicts = 0;
        $highConflicts = 0;

        foreach ($pendingRequests as $request) {
            $conflicts = $this->checkConflicts($request, $managerId);

            foreach ($conflicts as $conflict) {
                $totalConflicts++;

                if ($conflict['severity'] === 'critical') {
                    $criticalConflicts++;
                } elseif ($conflict['severity'] === 'high') {
                    $highConflicts++;
                }
            }
        }

        return [
            'total_conflicts' => $totalConflicts,
            'critical_conflicts' => $criticalConflicts,
            'high_conflicts' => $highConflicts,
            'pending_requests' => $pendingRequests->count(),
        ];
    }
}
