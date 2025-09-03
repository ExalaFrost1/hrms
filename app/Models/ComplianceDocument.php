<?php
// app/Models/ComplianceDocument.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceDocument extends Model
{
    protected $table = 'compliance_documents';

    protected $fillable = [
        'employee_id',
        'document_type',
        'document_name',
        'file_path',
        'submission_date',
        'expiry_date',
        'status',
        'notes',
        'verified_by',
        'verified_date'
    ];

    protected $casts = [
        'submission_date' => 'date',
        'expiry_date' => 'date',
        'verified_date' => 'date'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(): bool
    {
        return $this->expiry_date && $this->expiry_date->diffInDays() <= 30;
    }
}
