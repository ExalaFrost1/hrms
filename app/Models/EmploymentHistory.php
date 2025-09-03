<?php
// app/Models/EmploymentHistory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EmploymentHistory extends Model
{
    protected $fillable = [
        'employee_id',
        'joining_date',
        'probation_end_date',
        'initial_department',
        'initial_role',
        'initial_grade',
        'reporting_manager',
        'current_department',
        'current_role',
        'current_grade',
        'current_manager',
        'initial_salary',
        'current_salary',
        'employment_type',
    ];
protected $table = 'employment_history';
    protected $casts = [
        'joining_date' => 'date',
        'probation_end_date' => 'date',
        'initial_salary' => 'decimal:2',
        'current_salary' => 'decimal:2',
    ];

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }
}
