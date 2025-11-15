<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerDelegation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'manager_id',
        'delegate_manager_id',
        'start_date',
        'end_date',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the manager who is delegating authority.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the delegate manager (backup approver).
     */
    public function delegate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delegate_manager_id');
    }

    /**
     * Check if this delegation is currently active.
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = Carbon::today();

        return $today->between($this->start_date, $this->end_date);
    }

    /**
     * Get the active delegate for a manager on a specific date.
     *
     * @param  int  $managerId
     * @param  Carbon|string|null  $date
     * @return User|null
     */
    public static function getActiveDelegate(int $managerId, $date = null): ?User
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        $delegation = static::where('manager_id', $managerId)
            ->where('is_active', true)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        return $delegation?->delegate;
    }

    /**
     * Scope a query to only include active delegations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include delegations for a specific manager.
     */
    public function scopeForManager($query, int $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    /**
     * Scope a query to only include delegations where a user is the delegate.
     */
    public function scopeAsDelegate($query, int $delegateManagerId)
    {
        return $query->where('delegate_manager_id', $delegateManagerId);
    }

    /**
     * Scope a query to only include current delegations (within date range).
     */
    public function scopeCurrent($query)
    {
        $today = Carbon::today();

        return $query->where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today);
    }

    /**
     * Get formatted date range for display.
     */
    public function getDateRangeAttribute(): string
    {
        return $this->start_date->format('M j, Y') . ' - ' . $this->end_date->format('M j, Y');
    }
}
