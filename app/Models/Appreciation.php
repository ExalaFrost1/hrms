<?php
// app/Models/Appreciation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appreciation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'appreciation_number',
        'appreciation_type',
        'category',
        'achievement_date',
        'recognition_date',
        'nominated_by',
        'approved_by',
        'title',
        'description',
        'impact_description',
        'recognition_value',
        'public_recognition',
        'team_members_involved',
        'skills_demonstrated',
        'achievement_metrics',
        'peer_nominations',
        'employee_response',
        'hr_notes',
        'status',
        'publication_date',
        'supporting_documents',
    ];

    protected $casts = [
        'achievement_date' => 'date',
        'recognition_date' => 'date',
        'publication_date' => 'date',
        'public_recognition' => 'boolean',
        'recognition_value' => 'decimal:2',
        'supporting_documents' => 'array',
        'skills_demonstrated' => 'array',
        'team_members_involved' => 'array',
        'peer_nominations' => 'array',
        'achievement_metrics' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Generate appreciation number automatically
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($appreciation) {
            if (!$appreciation->appreciation_number) {
                $lastAppreciation = static::where('employee_id', $appreciation->employee_id)
                    ->orderBy('appreciation_number', 'desc')
                    ->first();

                $appreciation->appreciation_number = $lastAppreciation ? $lastAppreciation->appreciation_number + 1 : 1;
            }
        });
    }

    public function getAppreciationNumberDisplayAttribute(): string
    {
        return "Recognition #{$this->appreciation_number}";
    }

    public function getCategoryColorAttribute(): string
    {
        return match($this->category) {
            'exceptional_performance' => 'success',
            'innovation' => 'info',
            'leadership' => 'warning',
            'teamwork' => 'primary',
            'customer_service' => 'success',
            'problem_solving' => 'info',
            'mentoring' => 'warning',
            'milestone_achievement' => 'success',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'pending_approval' => 'warning',
            'approved' => 'success',
            'published' => 'info',
            'archived' => 'gray',
            default => 'gray'
        };
    }

    public function getRecognitionValueDisplayAttribute(): string
    {
        if (!$this->recognition_value) {
            return 'Recognition Only';
        }

        return '$' . number_format($this->recognition_value, 2);
    }
}
