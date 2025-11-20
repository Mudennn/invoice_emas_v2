<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundNoteItem extends Model
{
    use HasFactory;

    protected $table = 'refund_note_items';

    protected $primaryKey = 'id';

    protected $fillable = [
        'invoice_id',
        'refund_note_id',
        'reference_no',
        'particulars',
        'weight',
        'wastage',
        'total_weight',
        'gold_price_id',
        'gold',
        'workmanship',
        'total',
        // 'sst',
        // 'grand_total',
        'subtotal',
        'remark',
        'custom_reference',
        'pure_gold',
        'item_type',
        'status',
        'created_by',
        'updated_by',
        'currency_code',
        'classification',
        'tax_type',
        'tax_rate',
        'tax_amount',
        'tax_exemption',
        'amount_tax_exemption',
        'quantity',
        'pair',
        'unit_price',
        'remark_total',
        'kt'
    ];

    public function creditNote(): BelongsTo
    {
        return $this->belongsTo(RefundNote::class);
    }

    public function goldPrice(): BelongsTo
    {
        return $this->belongsTo(GoldPrice::class);
    }
} 