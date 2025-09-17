<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class EmployeeLeaveBalance extends Model
{
    protected $fillable = [
        'employee_name',
        'discord_user_id',
        'date_of_joining',
        'employment_type',
        'annual_entitled',
        'annual_taken',
        'annual_balance',
        'sick_entitled',
        'sick_taken',
        'sick_balance',
        'bereavement_entitled',
        'bereavement_taken',
        'bereavement_balance',
        'year',
    ];

    protected $casts = [
        'date_of_joining' => 'date',
        'annual_entitled' => 'decimal:2',
        'annual_taken' => 'decimal:2',
        'annual_balance' => 'decimal:2',
        'sick_entitled' => 'decimal:2',
        'sick_taken' => 'decimal:2',
        'sick_balance' => 'decimal:2',
        'bereavement_entitled' => 'decimal:2',
        'bereavement_taken' => 'decimal:2',
        'bereavement_balance' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($balance) {
            if (!$balance->year) {
                $balance->year = now()->year;
            }

            // Auto-calculate balances
            $balance->annual_balance = $balance->annual_entitled - $balance->annual_taken;
            $balance->sick_balance = $balance->sick_entitled - $balance->sick_taken;
            $balance->bereavement_balance = $balance->bereavement_entitled - $balance->bereavement_taken;
        });

        static::updating(function ($balance) {
            // Recalculate balances when entitled or taken values change
            $balance->annual_balance = $balance->annual_entitled - $balance->annual_taken;
            $balance->sick_balance = $balance->sick_entitled - $balance->sick_taken;
            $balance->bereavement_balance = $balance->bereavement_entitled - $balance->bereavement_taken;
        });
    }

    public function discordUserMapping()
    {
        return $this->belongsTo(DiscordUserMapping::class, 'discord_user_id', 'discord_user_id');
    }

    public function employee()
    {
        return $this->hasOneThrough(
            Employee::class,
            DiscordUserMapping::class,
            'discord_user_id',
            'id',
            'discord_user_id',
            'employee_id'
        );
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'employee_name', 'employee_name');
    }

    // Get or create balance record for current year
    public static function getOrCreateCurrentBalance($discordUserId, $employeeName, $employmentType = 'full_time')
    {
        return static::firstOrCreate(
            [
                'discord_user_id' => $discordUserId,
                'year' => now()->year,
            ],
            [
                'employee_name' => $employeeName,
                'employment_type' => $employmentType,
                'date_of_joining' => now()->toDateString(),
                'annual_entitled' => config('leave.default_allocations.annual', 25),
                'sick_entitled' => config('leave.default_allocations.sick', 12),
                'bereavement_entitled' => config('leave.default_allocations.bereavement', 5),
            ]
        );
    }

    // Update leave usage after approval
    public function updateLeaveUsage($leaveType, $days)
    {
        switch ($leaveType) {
            case 'annual':
                $this->annual_taken += $days;
                break;
            case 'sick':
                $this->sick_taken += $days;
                break;
            case 'bereavement':
                $this->bereavement_taken += $days;
                break;
        }

        $this->save();
    }

    // Check if sufficient balance exists
    public function hasSufficientBalance($leaveType, $days)
    {
        switch ($leaveType) {
            case 'annual':
                return $this->annual_balance >= $days;
            case 'sick':
                return $this->sick_balance >= $days;
            case 'bereavement':
                return $this->bereavement_balance >= $days;
            default:
                return false;
        }
    }

    // Get balance array in the format expected by your Discord bot
    public function getBalanceArray()
    {
        return [
            'employee_name' => $this->employee_name,
            'employment_type' => $this->employment_type,
            'annual' => [
                'total' => (float)$this->annual_entitled,
                'used' => (float)$this->annual_taken,
                'remaining' => (float)$this->annual_balance,
            ],
            'sick' => [
                'total' => (float)$this->sick_entitled,
                'used' => (float)$this->sick_taken,
                'remaining' => (float)$this->sick_balance,
            ],
            'bereavement' => [
                'total' => (float)$this->bereavement_entitled,
                'used' => (float)$this->bereavement_taken,
                'remaining' => (float)$this->bereavement_balance,
            ],
        ];
    }

    // Scope for current year
    public function scopeCurrentYear($query)
    {
        return $query->where('year', now()->year);
    }

    // Scope for specific user
    public function scopeByUser($query, $discordUserId)
    {
        return $query->where('discord_user_id', $discordUserId);
    }
}
