<?php
// app/Models/LeaveAttendance.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveAttendance extends Model
{
    protected $table = 'leave_attendance';

    protected $fillable = [
        'employee_id',
        'annual_leave_quota',
        'annual_leave_used',
        'sick_leave_quota',
        'sick_leave_used',
        'casual_leave_used',
        'bereavement_leave_used',
        'unpaid_leave_used',
        'average_login_hours',
        'on_time_attendance_rate',
        'year'
    ];

    protected $casts = [
        'average_login_hours' => 'decimal:2',
        'on_time_attendance_rate' => 'decimal:2'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getAnnualLeaveRemaining(): int
    {
        return $this->annual_leave_quota - $this->annual_leave_used;
    }

    public function getSickLeaveRemaining(): int
    {
        return $this->sick_leave_quota - $this->sick_leave_used;
    }

    public function getBereavementLeaveRemaining(): int
    {
        return 3 - $this->bereavement_leave_used; // 3 days max
    }
}
