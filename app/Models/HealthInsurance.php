<?php
// app/Models/HealthInsurance.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HealthInsurance extends Model
{
    protected $table = 'health_insurance';

    protected $fillable = [
        'employee_id',
        'provider_name',
        'policy_number',
        'coverage_details',
        'policy_start_date',
        'policy_end_date',
        'annual_premium',
        'annual_checkup_used',
        'last_checkup_date'
    ];

    protected $casts = [
        'coverage_details' => 'array',
        'policy_start_date' => 'date',
        'policy_end_date' => 'date',
        'annual_premium' => 'decimal:2',
        'annual_checkup_used' => 'boolean',
        'last_checkup_date' => 'date'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class);
    }
}
