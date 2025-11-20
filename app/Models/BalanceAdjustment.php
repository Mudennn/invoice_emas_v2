<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceAdjustment extends Model
{
    protected $table = 'balance_adjustments';

    protected $primaryKey = 'id';

    protected $fillable = [
        'company_name',
        'effective_date',
        'adjustment_amount',
        'adjustment_type',
        'description',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'effective_date' => 'date',
        'adjustment_amount' => 'decimal:2',
        'status' => 'integer'
    ];
}