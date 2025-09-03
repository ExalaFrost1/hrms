<?php
// app/Models/LeaveRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_requested',
        'reason',
        'status',
        'approved_by',
        'rejection_reason',
        'approved_at',
        'documents'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'documents' => 'array'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Calculate working days between dates
    public function calculateWorkingDays(): int
    {
        $start = $this->start_date;
        $end = $this->end_date;
        $days = 0;

        while ($start <= $end) {
            if ($start->isWeekday()) {
                $days++;
            }
            $start = $start->addDay();
        }

        return $days;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leaveRequest) {
            $leaveRequest->days_requested = $leaveRequest->calculateWorkingDays();
        });
    }
}
