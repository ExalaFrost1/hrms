<?php
// app/Models/CompensationHistory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompensationHistory extends Model
{
    protected $fillable = [
        'employee_id',
        'effective_date',
        'action_type',
        'new_salary',
        'previous_salary',
        'bonus_amount',
        'incentive_amount',
        'adjustment_amount',
        'remarks',
        'approved_by'
    ];
    protected $table = "compensation_history";
    protected $casts = [
        'effective_date' => 'date',
        'new_salary' => 'decimal:2',
        'previous_salary' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'incentive_amount' => 'decimal:2',
        'adjustment_amount' => 'decimal:2'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
