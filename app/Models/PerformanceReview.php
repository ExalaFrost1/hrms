<?php
// app/Models/PerformanceReview.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'key_achievements',
        'skills_demonstrated',
        'supporting_documents',
        'reviewed_by',
        'status'
    ];

    protected $casts = [
        'review_date' => 'date',
        'goal_completion_rate' => 'decimal:2',
        'overall_rating' => 'decimal:1',
        'key_achievements' => 'array',
        'skills_demonstrated' => 'array',
        'supporting_documents' => 'array'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Add scope for filtering by status
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    // Add scope for filtering by rating
    public function scopeHighPerformance($query)
    {
        return $query->where('overall_rating', '>=', 4.0);
    }
}
