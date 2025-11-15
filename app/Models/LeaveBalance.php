<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveBalance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'leave_type',
        'total_allocated',
        'used',
        'pending',
        'available',
        'year',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_allocated' => 'decimal:2',
        'used' => 'decimal:2',
        'pending' => 'decimal:2',
        'available' => 'decimal:2',
        'year' => 'integer',
    ];

    /**
     * Get the user that owns the leave balance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the history records for this balance.
     */
    public function balanceHistory(): HasMany
    {
        return $this->hasMany(LeaveBalanceHistory::class);
    }

    /**
     * Get pending leave requests affecting this balance.
     */
    public function pendingRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'user_id', 'user_id')
            ->where('leave_type', $this->leave_type)
            ->where('status', 'pending');
    }

    /**
     * Update the available balance based on used and pending amounts.
     */
    public function updateAvailable(): void
    {
        $this->available = $this->total_allocated - $this->used - $this->pending;
        $this->save();
    }

    /**
     * Deduct an amount from the balance (when request is approved).
     */
    public function deductBalance(float $amount): void
    {
        $this->used += $amount;
        $this->pending -= $amount;
        $this->updateAvailable();
    }

    /**
     * Restore an amount to the balance (when request is cancelled/denied).
     */
    public function restoreBalance(float $amount, bool $fromUsed = false): void
    {
        if ($fromUsed) {
            $this->used -= $amount;
        } else {
            $this->pending -= $amount;
        }
        $this->updateAvailable();
    }

    /**
     * Reserve an amount from the balance (when request is submitted).
     */
    public function reserveBalance(float $amount): void
    {
        $this->pending += $amount;
        $this->updateAvailable();
    }

    /**
     * Record a balance change in history.
     */
    public function recordBalanceChange(
        float $amount,
        string $changeType,
        int $performedByUserId,
        ?int $leaveRequestId = null,
        ?string $notes = null
    ): LeaveBalanceHistory {
        return $this->balanceHistory()->create([
            'change_amount' => $amount,
            'change_type' => $changeType,
            'performed_by_user_id' => $performedByUserId,
            'leave_request_id' => $leaveRequestId,
            'notes' => $notes,
        ]);
    }

    /**
     * Scope a query to only include balances for a specific year.
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope a query to only include balances for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include balances for a specific leave type.
     */
    public function scopeForLeaveType($query, string $leaveType)
    {
        return $query->where('leave_type', $leaveType);
    }

    /**
     * Get formatted balance for display.
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->available, 1) . ' / ' . number_format($this->total_allocated, 1) . ' days';
    }

    /**
     * Get the balance percentage used.
     */
    public function getPercentageUsedAttribute(): int
    {
        if ($this->total_allocated == 0) {
            return 0;
        }

        return (int) (($this->used / $this->total_allocated) * 100);
    }

    /**
     * Check if the balance is sufficient for a request.
     */
    public function hasSufficientBalance(float $requestedDays): bool
    {
        return $this->available >= $requestedDays;
    }
}
