<?php
// app/Models/PerformanceReview.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PerformanceReview extends Model
{
    protected $fillable = [
        'employee_id',
        'review_period',
        'review_date',
        'goal_completion_rate',
        'overall_rating',
        'manager_feedback',
        'peer_feedback',
        'self_assessment',
        'areas_of_strength',
        'areas_for_improvement',
        'development_goals',
        'reviewed_by',
        'status'
    ];

    protected $casts = [
        'review_date' => 'date',
        'goal_completion_rate' => 'decimal:2',
        'overall_rating' => 'decimal:1'
    ];
    protected $table = 'performance_reviews';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
