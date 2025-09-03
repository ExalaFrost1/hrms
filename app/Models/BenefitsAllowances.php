<?php
// app/Models/BenefitsAllowances.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BenefitsAllowances extends Model
{
    protected $table = 'benefits_allowances';

    protected $fillable = [
        'employee_id',
        'internet_allowance',
        'medical_allowance',
        'home_office_setup',
        'home_office_setup_claimed',
        'laptop_issued_date',
        'laptop_model',
        'laptop_serial',
        'birthday_allowance_claimed',
        'year'
    ];

    protected $casts = [
        'internet_allowance' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'home_office_setup' => 'decimal:2',
        'home_office_setup_claimed' => 'boolean',
        'laptop_issued_date' => 'date',
        'birthday_allowance_claimed' => 'boolean'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
