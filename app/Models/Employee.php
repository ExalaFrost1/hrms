<?php
// app/Models/Employee.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Employee extends Model
{
    use HasFactory, SoftDeletes, HasRoles, LogsActivity;

    protected $fillable = [
        'employee_id',
        'full_name',
        'email',
        'username',
        'password',
        'status',
        'profile_photo'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    // Relationships
    public function personalInfo(): HasOne
    {
        return $this->hasOne(PersonalInformation::class);
    }

    public function employmentHistory(): HasOne
    {
        return $this->hasOne(EmploymentHistory::class);
    }

    public function compensationHistory(): HasMany
    {
        return $this->hasMany(CompensationHistory::class);
    }

    public function careerProgression(): HasMany
    {
        return $this->hasMany(CareerProgression::class);
    }

    public function conductCompliance(): HasMany
    {
        return $this->hasMany(ConductCompliance::class);
    }

    public function leaveAttendance(): HasMany
    {
        return $this->hasMany(LeaveAttendance::class);
    }

    public function leaveRequests(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            LeaveRequest::class,
            DiscordUserMapping::class,
            'employee_id',
            'employee_name',
            'id',
            'discord_username'
        );
    }

    public function benefitsAllowances(): HasMany
    {
        return $this->hasMany(BenefitsAllowances::class);
    }

    public function learningDevelopment(): HasMany
    {
        return $this->hasMany(LearningDevelopment::class);
    }

    public function performanceReviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class);
    }

    public function assetManagement(): HasMany
    {
        return $this->hasMany(AssetManagement::class);
    }

    public function digitalAccess(): HasMany
    {
        return $this->hasMany(DigitalAccess::class);
    }

    public function healthInsurance(): HasMany
    {
        return $this->hasMany(HealthInsurance::class);
    }

    public function insuranceClaims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    public function employeeEngagement(): HasMany
    {
        return $this->hasMany(EmployeeEngagement::class);
    }

    public function successionPlanning(): HasMany
    {
        return $this->hasMany(SuccessionPlanning::class);
    }

    public function complianceDocuments(): HasMany
    {
        return $this->hasMany(ComplianceDocument::class);
    }

    public function exitDetails(): HasOne
    {
        return $this->hasOne(ExitDetail::class);
    }

    // Auto-generate employee ID
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            $lastEmployee = Employee::withTrashed()->orderBy('id', 'desc')->first();
            $nextId = $lastEmployee ? $lastEmployee->id + 1 : 1;
            $employee->employee_id = 'EMP' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        });
    }

    public static function generateEmployeeId()
    {
        $year = date('Y');
        $lastEmployee = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastEmployee && preg_match('/EMP-' . $year . '-(\d+)/', $lastEmployee->employee_id, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return 'EMP-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // Computed Properties
    public function getCurrentAge(): int
    {
        return $this->personalInfo?->date_of_birth
            ? now()->diffInYears($this->personalInfo->date_of_birth)
            : 0;
    }

    public function getTenureInMonths(): int
    {
        return $this->employmentHistory?->joining_date
            ? now()->diffInMonths($this->employmentHistory->joining_date)
            : 0;
    }

    public function getCurrentSalary(): float
    {
        return $this->compensationHistory()
            ->orderBy('effective_date', 'desc')
            ->first()?->new_salary ?? 0;
    }
    public function discordUserMapping(): HasOne
    {
        return $this->hasOne(DiscordUserMapping::class);
    }

    public function dailyAttendance(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            EmployeeDailyAttendance::class,
            DiscordUserMapping::class,
            'employee_id', // Foreign key on discord_user_mappings table
            'discord_user_id', // Foreign key on employee_daily_attendance table
            'id', // Local key on employees table
            'discord_user_id' // Local key on discord_user_mappings table
        );
    }

    public function leaveBalance(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            EmployeeLeaveBalance::class,
            DiscordUserMapping::class,
            'employee_id',
            'discord_user_id',
            'id',
            'discord_user_id'
        )->where('year', now()->year);
    }

    public function leaveBalances(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            EmployeeLeaveBalance::class,
            DiscordUserMapping::class,
            'employee_id',
            'discord_user_id',
            'id',
            'discord_user_id'
        );
    }

    /**
     * Get current attendance status
     */
    public function getCurrentAttendanceStatus()
    {
        if (!$this->discordUserMapping) {
            return null;
        }

        return $this->dailyAttendance()
            ->where('attendance_date', now()->toDateString())
            ->first();
    }

    /**
     * Get leave balance for current year
     */
    public function getCurrentLeaveBalance()
    {
        return $this->leaveBalance;
    }

    /**
     * Check if employee is currently checked in
     */
    public function isCheckedIn(): bool
    {
        $attendance = $this->getCurrentAttendanceStatus();
        return $attendance && in_array($attendance->status, ['checked_in', 'on_break', 'screen_sharing']);
    }

    /**
     * Get total work hours for a date range
     */
    public function getTotalWorkHours($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now();

        $attendance = $this->dailyAttendance()
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        $totalMinutes = 0;
        foreach ($attendance as $record) {
            $time = $record->total_work_time;
            if ($time && $time !== '00:00:00') {
                $parts = explode(':', $time);
                $totalMinutes += (int)$parts[0] * 60 + (int)$parts[1];
            }
        }

        return round($totalMinutes / 60, 2);
    }

    /**
     * Scope for employees with Discord mapping
     */
    public function scopeWithDiscordMapping($query)
    {
        return $query->whereHas('discordUserMapping');
    }

    /**
     * Scope for employees currently checked in
     */
    public function scopeCheckedIn($query)
    {
        return $query->whereHas('dailyAttendance', function ($q) {
            $q->where('attendance_date', now()->toDateString())
                ->whereIn('status', ['checked_in', 'on_break', 'screen_sharing']);
        });
    }
    // Add this method inside your Employee model class
    public function warnings(): HasMany
    {
        return $this->hasMany(Warning::class)->orderBy('warning_number', 'desc');
    }

// You might also want to add these helper methods for getting warning counts
    public function getWarningCountAttribute(): int
    {
        return $this->warnings()->count();
    }

    public function getActiveWarningCountAttribute(): int
    {
        return $this->warnings()->where('status', 'active')->count();
    }

    public function getLatestWarningAttribute(): ?Warning
    {
        return $this->warnings()->first();
    }

    public function hasActiveWarnings(): bool
    {
        return $this->warnings()->where('status', 'active')->exists();
    }
    // Add this method inside your Employee model class
    public function appreciations(): HasMany
    {
        return $this->hasMany(Appreciation::class)->orderBy('appreciation_number', 'desc');
    }

// You might also want to add these helper methods for getting appreciation counts
    public function getAppreciationCountAttribute(): int
    {
        return $this->appreciations()->count();
    }

    public function getPublishedAppreciationCountAttribute(): int
    {
        return $this->appreciations()->where('status', 'published')->count();
    }

    public function getLatestAppreciationAttribute(): ?Appreciation
    {
        return $this->appreciations()->first();
    }

    public function getTotalRecognitionValueAttribute(): float
    {
        return $this->appreciations()
            ->whereNotNull('recognition_value')
            ->sum('recognition_value');
    }

    public function hasRecentAppreciations(): bool
    {
        return $this->appreciations()
            ->where('recognition_date', '>=', now()->subMonths(6))
            ->exists();
    }
    // Add this method inside your Employee model class
    public function performanceImprovementPlans(): HasMany
    {
        return $this->hasMany(PerformanceImprovementPlan::class)->orderBy('pip_number', 'desc');
    }

// Alias for easier access
    public function pips(): HasMany
    {
        return $this->performanceImprovementPlans();
    }

// You might also want to add these helper methods for getting PIP counts and status
    public function getPipCountAttribute(): int
    {
        return $this->performanceImprovementPlans()->count();
    }

    public function getActivePipCountAttribute(): int
    {
        return $this->performanceImprovementPlans()->where('status', 'active')->count();
    }

    public function getLatestPipAttribute(): ?PerformanceImprovementPlan
    {
        return $this->performanceImprovementPlans()->first();
    }

    public function hasActivePip(): bool
    {
        return $this->performanceImprovementPlans()->where('status', 'active')->exists();
    }

    public function hasSuccessfulPips(): bool
    {
        return $this->performanceImprovementPlans()->where('status', 'successful')->exists();
    }

    public function getUnsuccessfulPipCountAttribute(): int
    {
        return $this->performanceImprovementPlans()
            ->whereIn('status', ['unsuccessful', 'terminated'])
            ->count();
    }

    public function getPipSuccessRateAttribute(): float
    {
        $totalCompleted = $this->performanceImprovementPlans()
            ->whereIn('status', ['successful', 'unsuccessful', 'terminated'])
            ->count();

        if ($totalCompleted === 0) {
            return 0;
        }

        $successful = $this->performanceImprovementPlans()
            ->where('status', 'successful')
            ->count();

        return ($successful / $totalCompleted) * 100;
    }
}
