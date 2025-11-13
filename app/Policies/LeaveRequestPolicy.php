<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     * All authenticated users can view their own leave requests.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Users can view their own requests, managers can view their team's requests.
     */
    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        // User can view their own request
        if ($leaveRequest->user_id === $user->id) {
            return true;
        }

        // Manager can view requests from their direct reports
        if ($user->isManager() && $leaveRequest->manager_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     * All authenticated users can create leave requests.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Only the owner can update their own pending request.
     */
    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        return $leaveRequest->user_id === $user->id && $leaveRequest->isPending();
    }

    /**
     * Determine whether the user can cancel the model.
     * Only the owner can cancel their own pending or approved request.
     */
    public function cancel(User $user, LeaveRequest $leaveRequest): bool
    {
        return $leaveRequest->user_id === $user->id &&
               ($leaveRequest->isPending() || $leaveRequest->isApproved());
    }

    /**
     * Determine whether the user can delete the model.
     * Users cannot delete leave requests (use cancel instead).
     */
    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LeaveRequest $leaveRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LeaveRequest $leaveRequest): bool
    {
        return false;
    }
}
