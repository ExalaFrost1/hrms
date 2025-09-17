<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class DiscordUserMapping extends Model
{
    protected $fillable = [
        'employee_id',
        'discord_user_id',
        'discord_username',
        'discord_display_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function dailyAttendance()
    {
        return $this->hasMany(EmployeeDailyAttendance::class, 'discord_user_id', 'discord_user_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'employee_name', 'discord_username');
    }

    public function leaveBalance()
    {
        return $this->hasOne(EmployeeLeaveBalance::class, 'discord_user_id', 'discord_user_id')
            ->where('year', now()->year);
    }

    public function leaveBalances()
    {
        return $this->hasMany(EmployeeLeaveBalance::class, 'discord_user_id', 'discord_user_id');
    }

    // Get or create mapping for Discord user
    public static function getOrCreateMapping($discordUserId, $discordUsername, $discordDisplayName = null, $employeeId = null)
    {
        return static::updateOrCreate(
            ['discord_user_id' => $discordUserId],
            [
                'discord_username' => $discordUsername,
                'discord_display_name' => $discordDisplayName ?: $discordUsername,
                'employee_id' => $employeeId,
                'is_active' => true,
            ]
        );
    }

    // Scope for active mappings
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
