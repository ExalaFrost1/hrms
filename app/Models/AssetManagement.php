<?php
// app/Models/AssetManagement.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetManagement extends Model
{
    protected $table = 'asset_management';

    protected $fillable = [
        'employee_id',
        'asset_type',
        'asset_name',
        'model',
        'serial_number',
        'issued_date',
        'return_date',
        'condition_when_issued',
        'condition_when_returned',
        'purchase_value',
        'notes',
        'status'
    ];

    protected $casts = [
        'issued_date' => 'date',
        'return_date' => 'date',
        'purchase_value' => 'decimal:2'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
