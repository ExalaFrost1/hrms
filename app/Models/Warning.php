<?php
// app/Models/Warning.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warning extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'warning_number',
        'warning_type',
        'severity_level',
        'incident_date',
        'warning_date',
        'issued_by',
        'subject',
        'description',
        'incident_location',
        'witnesses',
        'previous_discussions',
        'expected_improvement',
        'consequences_if_repeated',
        'follow_up_date',
        'employee_acknowledgment',
        'employee_comments',
        'hr_notes',
        'status',
        'resolution_date',
        'supporting_documents',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'warning_date' => 'date',
        'follow_up_date' => 'date',
        'resolution_date' => 'date',
        'employee_acknowledgment' => 'boolean',
        'supporting_documents' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Generate warning number automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($warning) {
            if (!$warning->warning_number) {
                $lastWarning = static::where('employee_id', $warning->employee_id)
                    ->orderBy('warning_number', 'desc')
                    ->first();

                $warning->warning_number = $lastWarning ? $lastWarning->warning_number + 1 : 1;
            }
        });
    }

    public function getWarningNumberDisplayAttribute(): string
    {
        return "Warning #{$this->warning_number}";
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity_level) {
            'minor' => 'warning',
            'moderate' => 'danger',
            'major' => 'danger',
            'critical' => 'danger',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'warning',
            'acknowledged' => 'info',
            'resolved' => 'success',
            'escalated' => 'danger',
            default => 'gray'
        };
    }
}
