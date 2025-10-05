<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'asset_code',
        'qr_code',
        'asset_name',
        'category_id',
        'brand',
        'model',
        'filename',
        'serial_number',
        'purchase_date',
        'purchase_price',
        'warranty_expiry',
        'condition_status',
        'location',
        'status'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(AssetAssignment::class)->where('status', 'active');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(AssetHistory::class);
    }

    public function incidentReports(): HasMany
    {
        return $this->hasMany(IncidentReport::class);
    }

    public function assignedUser()
    {
        return $this->hasOneThrough(
            Employee::class,
            AssetAssignment::class,
            'asset_id',
            'id',
            'id',
            'employee_id'
        )->where('asset_assignments.status', 'active');
    }
}
