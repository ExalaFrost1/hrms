<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class EmployeeDailyAttendance extends Model
{
    protected $fillable = [
        'discord_user_id',
        'attendance_date',
        'employee_name',
        'display_name',
        'status',
        'last_update',
        'total_work_time',
        'total_break_time',
        'screen_time',
        'check_in_time',
        'check_out_time',
        'break_start_time',
        'screen_share_start',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'last_update' => 'datetime',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'break_start_time' => 'datetime',
        'screen_share_start' => 'datetime',
    ];

    public function discordUserMapping()
    {
        return $this->belongsTo(DiscordUserMapping::class, 'discord_user_id', 'discord_user_id');
    }

    public function employee()
    {
        return $this->hasOneThrough(
            Employee::class,
            DiscordUserMapping::class,
            'discord_user_id', // Foreign key on discord_user_mappings table
            'id', // Foreign key on employees table
            'discord_user_id', // Local key on employee_daily_attendance table
            'employee_id' // Local key on discord_user_mappings table
        );
    }

    // Get or create today's attendance record
    public static function getOrCreateTodayRecord($discordUserId, $employeeName, $displayName = null)
    {
        return static::firstOrCreate(
            [
                'discord_user_id' => $discordUserId,
                'attendance_date' => now()->toDateString(),
            ],
            [
                'employee_name' => $employeeName,
                'display_name' => $displayName ?: $employeeName,
                'status' => 'offline',
                'last_update' => now(),
            ]
        );
    }

    // Update work time calculations
    public function updateWorkTime()
    {
        if ($this->check_in_time && $this->check_out_time) {
            $workMinutes = $this->check_in_time->diffInMinutes($this->check_out_time);
            $breakMinutes = $this->getBreakMinutes();
            $actualWorkMinutes = max(0, $workMinutes - $breakMinutes);

            $this->total_work_time = $this->minutesToTimeString($actualWorkMinutes);
            $this->total_break_time = $this->minutesToTimeString($breakMinutes);
        }
    }

    private function getBreakMinutes()
    {
        // This would need to be implemented based on your break tracking logic
        // For now, returning 0
        return 0;
    }

    private function minutesToTimeString($minutes)
    {
        $hours = intval($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d:00', $hours, $mins);
    }

    // Scope for getting attendance by date range
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    // Scope for getting attendance by user
    public function scopeByUser($query, $discordUserId)
    {
        return $query->where('discord_user_id', $discordUserId);
    }
}
