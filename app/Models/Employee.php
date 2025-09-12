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

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
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
}
