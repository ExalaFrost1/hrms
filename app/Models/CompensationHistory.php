<?php
// app/Models/CompensationHistory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CompensationHistory extends Model
{
    protected $fillable = [
        'employee_id',
        'effective_date',
        'action_type',
        'new_salary',
        'previous_salary',
        'bonus_amount',
        'incentive_amount',
        'adjustment_amount',
        'remarks',
        'approved_by'
    ];

    protected $table = "compensation_history";

    protected $casts = [
        'effective_date' => 'date',
        'new_salary' => 'decimal:2',
        'previous_salary' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'incentive_amount' => 'decimal:2',
        'adjustment_amount' => 'decimal:2'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Helper methods for better data handling

    /**
     * Get the primary amount for this compensation record
     */
    public function getPrimaryAmountAttribute(): ?float
    {
        return match($this->action_type) {
            'joining', 'increment', 'promotion' => $this->new_salary,
            'bonus' => $this->bonus_amount,
            'adjustment' => $this->adjustment_amount,
            default => null
        };
    }

    /**
     * Get the salary increase amount
     */
    public function getSalaryIncreaseAttribute(): ?float
    {
        if (in_array($this->action_type, ['increment', 'promotion']) && $this->previous_salary && $this->new_salary) {
            return $this->new_salary - $this->previous_salary;
        }
        return null;
    }

    /**
     * Get the percentage increase
     */
    public function getPercentageIncreaseAttribute(): ?float
    {
        if ($this->salary_increase && $this->previous_salary > 0) {
            return round(($this->salary_increase / $this->previous_salary) * 100, 2);
        }
        return null;
    }

    /**
     * Get total compensation value (salary + bonus + incentive)
     */
    public function getTotalCompensationAttribute(): float
    {
        return ($this->new_salary ?? 0) +
            ($this->bonus_amount ?? 0) +
            ($this->incentive_amount ?? 0) +
            ($this->adjustment_amount ?? 0);
    }

    /**
     * Check if this is a salary-affecting action
     */
    public function isSalaryChangeAttribute(): bool
    {
        return in_array($this->action_type, ['joining', 'increment', 'promotion', 'adjustment']);
    }

    /**
     * Scope for filtering by action type
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('action_type', $type);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeBetweenDates(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('effective_date', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by year
     */
    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->whereYear('effective_date', $year);
    }

    /**
     * Get the latest compensation record for an employee
     */
    public function scopeLatestForEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where('employee_id', $employeeId)
            ->orderBy('effective_date', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Validation rules for the model
     */
    public static function getValidationRules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'effective_date' => 'required|date|before_or_equal:today',
            'action_type' => 'required|in:joining,increment,promotion,bonus,adjustment',
            'new_salary' => 'nullable|numeric|min:0',
            'previous_salary' => 'nullable|numeric|min:0',
            'bonus_amount' => 'nullable|numeric|min:0',
            'incentive_amount' => 'nullable|numeric|min:0',
            'adjustment_amount' => 'nullable|numeric',
            'remarks' => 'nullable|string|max:1000',
            'approved_by' => 'nullable|string|max:255',
        ];
    }

    /**
     * Custom validation for business logic
     */
    public function validateBusinessRules(): array
    {
        $errors = [];

        // Check if new salary is required for certain action types
        if (in_array($this->action_type, ['joining', 'increment', 'promotion']) && !$this->new_salary) {
            $errors[] = 'New salary is required for ' . $this->action_type . ' actions.';
        }

        // Check if bonus amount is required for bonus action
        if ($this->action_type === 'bonus' && !$this->bonus_amount) {
            $errors[] = 'Bonus amount is required for bonus actions.';
        }

        // Check if adjustment amount is required for adjustment action
        if ($this->action_type === 'adjustment' && $this->adjustment_amount === null) {
            $errors[] = 'Adjustment amount is required for adjustment actions.';
        }

        // Check if new salary is greater than previous salary for increments and promotions
        if (in_array($this->action_type, ['increment', 'promotion']) &&
            $this->previous_salary &&
            $this->new_salary &&
            $this->new_salary <= $this->previous_salary) {
            $errors[] = 'New salary must be greater than previous salary for increments and promotions.';
        }

        return $errors;
    }

    /**
     * Boot method to add model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Auto-populate previous salary if not set
            if (in_array($model->action_type, ['increment', 'promotion']) && !$model->previous_salary) {
                $latestRecord = static::latestForEmployee($model->employee_id)
                    ->where('id', '!=', $model->id)
                    ->first();

                if ($latestRecord && $latestRecord->new_salary) {
                    $model->previous_salary = $latestRecord->new_salary;
                }
            }
        });
    }
}
