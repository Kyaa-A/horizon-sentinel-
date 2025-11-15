<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeavePolicy extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'policy_type',
        'leave_type',
        'config_json',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'config_json' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active policies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include policies of a specific type.
     */
    public function scopeOfType($query, string $policyType)
    {
        return $query->where('policy_type', $policyType);
    }

    /**
     * Scope a query to only include policies for a specific leave type.
     */
    public function scopeForLeaveType($query, ?string $leaveType)
    {
        if ($leaveType) {
            return $query->where(function ($q) use ($leaveType) {
                $q->where('leave_type', $leaveType)
                    ->orWhereNull('leave_type'); // Include policies that apply to all types
            });
        }

        return $query->whereNull('leave_type');
    }

    /**
     * Get the policy configuration.
     */
    public function getConfig(?string $key = null)
    {
        if ($key) {
            return $this->config_json[$key] ?? null;
        }

        return $this->config_json;
    }

    /**
     * Get human-readable policy type name.
     */
    public function getPolicyTypeNameAttribute(): string
    {
        return match ($this->policy_type) {
            'blackout_period' => 'Blackout Period',
            'minimum_notice' => 'Minimum Notice Required',
            'max_consecutive_days' => 'Maximum Consecutive Days',
            default => ucfirst(str_replace('_', ' ', $this->policy_type)),
        };
    }
}
