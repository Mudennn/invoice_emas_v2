<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SelfBilledInvoice extends Model
{
    protected $table = 'self_billed_invoices';

    protected $primaryKey = 'id';

    protected $fillable = ['company_name', 'self_billed_invoice_date', 'self_billed_invoice_no', 'goods_received_by', 'payment_received_by', 'type_code', 'signed_info', 'signature_value', 'x509_certificate', 'issuer_name', 'serial_number', 'einvoice_version', 'self_billed_invoice_uuid', 'status', 'created_by', 'updated_by'];

    // Relationship with InvoiceItems
    public function selfBilledInvoiceItems()
    {
        return $this->hasMany(SelfBilledInvoiceItem::class, 'self_billed_invoice_id', 'id')
            ->where('status', '0');
    }

    // Relationship with CustomerProfile
    public function customerProfile()
    {
        return $this->belongsTo(CustomerProfile::class, 'company_name', 'company_name');
    }

    // // Relationship with Payments
    // public function payments()
    // {
    //     return $this->hasMany(Payment::class);
    // }

    // // Relationship with CreditNotes
    // public function creditNotes(): HasMany
    // {
    //     return $this->hasMany(CreditNote::class, 'invoice_no', 'invoice_no');
    // }

    // // Relationship with DebitNotes
    // public function debitNotes(): HasMany
    // {
    //     return $this->hasMany(DebitNote::class, 'invoice_no', 'invoice_no');
    // }
}