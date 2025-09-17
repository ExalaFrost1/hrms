<?php
// app/Models/BenefitsAllowances.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BenefitsAllowances extends Model
{
    protected $table = 'benefits_allowances';

    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'internet_allowance',
        'medical_allowance',
        'home_office_setup',
        'home_office_setup_claimed',
        'birthday_allowance_claimed',
        'other_benefits'
    ];

    protected $casts = [
        'internet_allowance' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'home_office_setup' => 'decimal:2',
        'home_office_setup_claimed' => 'boolean',
        'birthday_allowance_claimed' => 'boolean',
        'other_benefits' => 'array',
        'year' => 'integer',
        'month' => 'integer'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Accessor for month name
    public function getMonthNameAttribute(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    // Accessor for formatted period
    public function getPeriodAttribute(): string
    {
        return $this->month_name . ' ' . $this->year;
    }

    // Calculate total monthly benefits including custom ones
    public function getTotalBenefitsAttribute(): float
    {
        $total = ($this->internet_allowance ?? 0) + ($this->medical_allowance ?? 0);

        if ($this->other_benefits && is_array($this->other_benefits)) {
            foreach ($this->other_benefits as $benefit) {
                $total += $benefit['benefit_value'] ?? 0;
            }
        }

        return $total;
    }

    // Get count of custom benefits
    public function getCustomBenefitsCountAttribute(): int
    {
        return $this->other_benefits ? count($this->other_benefits) : 0;
    }

    // Check if any custom benefits are claimed
    public function hasClaimedCustomBenefitsAttribute(): bool
    {
        if (!$this->other_benefits || !is_array($this->other_benefits)) {
            return false;
        }

        foreach ($this->other_benefits as $benefit) {
            if ($benefit['is_claimed'] ?? false) {
                return true;
            }
        }

        return false;
    }

    // Scope for filtering by year and month
    public function scopeForPeriod($query, int $year, int $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    // Scope for current month
    public function scopeCurrentMonth($query)
    {
        return $query->where('year', now()->year)->where('month', now()->month);
    }
}
