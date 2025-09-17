<?php
// app/Models/PerformanceImprovementPlan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceImprovementPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'performance_improvement_plans';

    protected $fillable = [
        'employee_id',
        'pip_number',
        'pip_type',
        'severity_level',
        'start_date',
        'end_date',
        'review_frequency',
        'initiated_by',
        'supervisor_assigned',
        'hr_representative',
        'title',
        'performance_concerns',
        'root_cause_analysis',
        'specific_objectives',
        'success_metrics',
        'required_actions',
        'support_provided',
        'training_requirements',
        'resources_allocated',
        'milestone_dates',
        'consequences_of_failure',
        'employee_acknowledgment',
        'employee_comments',
        'supervisor_notes',
        'hr_notes',
        'status',
        'completion_date',
        'final_outcome',
        'supporting_documents',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'completion_date' => 'date',
        'employee_acknowledgment' => 'boolean',
        'supporting_documents' => 'array',
        'specific_objectives' => 'array',
        'success_metrics' => 'array',
        'required_actions' => 'array',
        'milestone_dates' => 'array',
        'training_requirements' => 'array',
        'resources_allocated' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Generate PIP number automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pip) {
            if (!$pip->pip_number) {
                $lastPip = static::where('employee_id', $pip->employee_id)
                    ->orderBy('pip_number', 'desc')
                    ->first();

                $pip->pip_number = $lastPip ? $lastPip->pip_number + 1 : 1;
            }
        });
    }

    public function getPipNumberDisplayAttribute(): string
    {
        return "PIP #{$this->pip_number}";
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity_level) {
            'low' => 'info',
            'moderate' => 'warning',
            'high' => 'danger',
            'critical' => 'danger',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'active' => 'warning',
            'under_review' => 'info',
            'successful' => 'success',
            'unsuccessful' => 'danger',
            'terminated' => 'danger',
            'extended' => 'warning',
            default => 'gray'
        };
    }

    public function getDaysRemainingAttribute(): int
    {
        if ($this->status !== 'active' || !$this->end_date) {
            return 0;
        }

        return max(0, now()->diffInDays($this->end_date, false));
    }

    public function getProgressPercentageAttribute(): float
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $totalDays = $this->start_date->diffInDays($this->end_date);
        $elapsedDays = $this->start_date->diffInDays(now());

        if ($totalDays <= 0) {
            return 100;
        }

        return min(100, max(0, ($elapsedDays / $totalDays) * 100));
    }
}
