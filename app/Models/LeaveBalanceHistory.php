<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalanceHistory extends Model
{
    /**
     * Indicates if the model should be timestamped.
     * Only uses created_at, no updated_at (immutable records).
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'leave_balance_id',
        'change_amount',
        'change_type',
        'leave_request_id',
        'performed_by_user_id',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'change_amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leave_balance_history';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set created_at on creation
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }

    /**
     * Get the leave balance that owns this history record.
     */
    public function leaveBalance(): BelongsTo
    {
        return $this->belongsTo(LeaveBalance::class);
    }

    /**
     * Get the leave request associated with this change (if any).
     */
    public function leaveRequest(): BelongsTo
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    /**
     * Get the user who performed this action.
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }

    /**
     * Get formatted change amount with sign.
     */
    public function getFormattedChangeAttribute(): string
    {
        $sign = $this->change_amount > 0 ? '+' : '';
        return $sign . number_format($this->change_amount, 1);
    }

    /**
     * Get human-readable change type.
     */
    public function getChangeTypeLabel(): string
    {
        return match ($this->change_type) {
            'accrual' => 'Accrual',
            'consumption' => 'Leave Taken',
            'adjustment' => 'Manual Adjustment',
            'carryover' => 'Year-End Carryover',
            default => ucfirst($this->change_type),
        };
    }

    /**
     * Scope a query to only include records for a specific balance.
     */
    public function scopeForBalance($query, int $leaveBalanceId)
    {
        return $query->where('leave_balance_id', $leaveBalanceId);
    }

    /**
     * Scope a query to only include records of a specific change type.
     */
    public function scopeOfType($query, string $changeType)
    {
        return $query->where('change_type', $changeType);
    }
}
