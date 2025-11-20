<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $primaryKey = 'id';

    protected $fillable = ['company_name', 'invoice_date', 'invoice_no', 'goods_received_by', 'payment_received_by', 'type_code', 'signed_info', 'signature_value', 'x509_certificate', 'issuer_name', 'serial_number', 'einvoice_version', 'invoice_uuid', 'status', 'created_by', 'updated_by'];

    // Relationship with InvoiceItems
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id')
            ->where('status', '0');
    }

    // Relationship with CustomerProfile
    public function customerProfile()
    {
        return $this->belongsTo(CustomerProfile::class, 'company_name', 'company_name');
    }

    // Relationship with Payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Relationship with CreditNotes
    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class, 'invoice_no', 'invoice_no');
    }

    // Relationship with DebitNotes
    public function debitNotes(): HasMany
    {
        return $this->hasMany(DebitNote::class, 'invoice_no', 'invoice_no');
    }

    // Relationship with Einvoices (polymorphic)
    public function einvoices(): MorphMany
    {
        return $this->morphMany(Einvoice::class, 'documentable');
    }
}