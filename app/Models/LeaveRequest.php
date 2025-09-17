<?php
// app/Models/LeaveRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = [
        'request_id',
        'employee_name',
        'full_name',
        'email',
        'department',
        'leave_type',
        'half_day_period',
        'reason',
        'start_date',
        'end_date',
        'description',
        'status',
        'approver_username',
        'thread_id',
        'rejection_reason',
        'attachment_filename',
        'attachment_path',
        'calculated_days',
        'leave_category',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'calculated_days' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leaveRequest) {
            if (empty($leaveRequest->request_id)) {
                $leaveRequest->request_id = 'LR-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
            }

            // Auto-calculate days and category
            $leaveRequest->calculated_days = $leaveRequest->calculateLeaveDays();
            $leaveRequest->leave_category = $leaveRequest->categorizeLeavetype();
        });

        static::updating(function ($leaveRequest) {
            // Recalculate days if dates changed
            if ($leaveRequest->isDirty(['start_date', 'end_date', 'leave_type'])) {
                $leaveRequest->calculated_days = $leaveRequest->calculateLeaveDays();
            }
        });
    }

    public function discordUserMapping()
    {
        return $this->belongsTo(DiscordUserMapping::class, 'employee_name', 'discord_username');
    }

    public function employee()
    {
        return $this->hasOneThrough(
            Employee::class,
            DiscordUserMapping::class,
            'discord_username', // Foreign key on discord_user_mappings table
            'id', // Foreign key on employees table
            'employee_name', // Local key on leave_requests table
            'employee_id' // Local key on discord_user_mappings table
        );
    }

    // Calculate leave days based on dates and type
    public function calculateLeaveDays()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);
        $days = $startDate->diffInDays($endDate) + 1;

        // Adjust for half days
        if ($this->leave_type && str_contains(strtolower($this->leave_type), 'half')) {
            return $days * 0.5;
        }

        return $days;
    }

    // Categorize leave type based on reason (from your Python logic)
    public function categorizeLeavetype()
    {
        if (!$this->reason) {
            return 'annual';
        }

        $reasonLower = strtolower($this->reason);

        // Check for sick leave
        $sickKeywords = ['sick', 'illness', 'medical', 'doctor', 'hospital', 'fever', 'health'];
        foreach ($sickKeywords as $keyword) {
            if (str_contains($reasonLower, $keyword)) {
                return 'sick';
            }
        }

        // Check for bereavement leave
        $bereavementKeywords = ['bereavement', 'death', 'funeral'];
        foreach ($bereavementKeywords as $keyword) {
            if (str_contains($reasonLower, $keyword)) {
                return 'bereavement';
            }
        }

        // Default to annual leave
        return 'annual';
    }

    // Scope for pending requests
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for approved requests
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Scope for rejected requests
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Scope for filtering by employee
    public function scopeByEmployee($query, $employeeName)
    {
        return $query->where('employee_name', $employeeName);
    }

    // Scope for filtering by date range
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        });
    }
}
