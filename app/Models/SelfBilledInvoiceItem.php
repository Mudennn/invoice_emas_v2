<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelfBilledInvoiceItem extends Model
{
    protected $table = 'self_billed_invoice_items';

    protected $primaryKey = 'id';
   
    protected $fillable = [
        'self_billed_invoice_id',
        'reference_no',
        'particulars',
        'weight',
        'wastage',
        'total_weight',
        // 'gold_price_id',
        'gold',
        'workmanship',
        'total',
        'subtotal',
        'grand_total',
        'sst',
        'remark',
        'status',
        'created_by',
        'updated_by',
        'item_type',
        'custom_reference',
        'pure_gold',
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

    // Relationship with Invoice
    public function selfBilledInvoice()
    {
        return $this->belongsTo(SelfBilledInvoice::class, 'self_billed_invoice_id', 'id');
    }

    // Add relationship
    // public function goldPrice()
    // {
    //     return $this->belongsTo(GoldPrice::class, 'gold_price_id', 'id');
    // }
}