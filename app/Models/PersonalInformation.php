<?php
// app/Models/PersonalInformation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalInformation extends Model
{
    protected $fillable = [
        'employee_id',
        'date_of_birth',
        'age',
        'gender',
        'marital_status',
        'phone_number',
        'personal_email',
        'residential_address',
        'city',
        'state',
        'postal_code',
        'country',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'national_id',
        'passport_number',
        'tax_number',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Auto-calculate age when date_of_birth is set
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($personalInfo) {
            if ($personalInfo->date_of_birth) {
                $personalInfo->age = now()->diffInYears($personalInfo->date_of_birth);
            }
        });
    }
}
