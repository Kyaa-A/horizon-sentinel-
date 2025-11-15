<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CompanyHoliday extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'date',
        'is_recurring',
        'region',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
    ];

    /**
     * Get holidays within a specific date range.
     *
     * @param  Carbon|string  $startDate
     * @param  Carbon|string  $endDate
     * @param  string|null  $region
     * @return Collection
     */
    public static function getHolidaysInRange($startDate, $endDate, ?string $region = null): Collection
    {
        $query = static::query()
            ->whereBetween('date', [
                Carbon::parse($startDate)->format('Y-m-d'),
                Carbon::parse($endDate)->format('Y-m-d'),
            ]);

        if ($region) {
            $query->where(function ($q) use ($region) {
                $q->where('region', $region)
                    ->orWhereNull('region'); // Include global holidays
            });
        } else {
            $query->whereNull('region'); // Only global holidays
        }

        return $query->orderBy('date')->get();
    }

    /**
     * Check if a specific date is a holiday.
     *
     * @param  Carbon|string  $date
     * @param  string|null  $region
     * @return bool
     */
    public static function isHoliday($date, ?string $region = null): bool
    {
        $query = static::where('date', Carbon::parse($date)->format('Y-m-d'));

        if ($region) {
            $query->where(function ($q) use ($region) {
                $q->where('region', $region)
                    ->orWhereNull('region');
            });
        } else {
            $query->whereNull('region');
        }

        return $query->exists();
    }

    /**
     * Count working days between two dates, excluding holidays.
     *
     * @param  Carbon|string  $startDate
     * @param  Carbon|string  $endDate
     * @param  string|null  $region
     * @return int
     */
    public static function countWorkingDays($startDate, $endDate, ?string $region = null): int
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $holidays = static::getHolidaysInRange($start, $end, $region)
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();

        $workingDays = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            // Count if it's a weekday and not a holiday
            if (!$current->isWeekend() && !in_array($current->format('Y-m-d'), $holidays)) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    /**
     * Scope a query to only include upcoming holidays.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date');
    }

    /**
     * Scope a query to only include holidays for a specific region.
     */
    public function scopeForRegion($query, ?string $region)
    {
        if ($region) {
            return $query->where(function ($q) use ($region) {
                $q->where('region', $region)
                    ->orWhereNull('region');
            });
        }

        return $query->whereNull('region');
    }

    /**
     * Scope a query to only include holidays for a specific year.
     */
    public function scopeForYear($query, int $year)
    {
        return $query->whereYear('date', $year);
    }

    /**
     * Get formatted date for display.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('F j, Y');
    }
}
