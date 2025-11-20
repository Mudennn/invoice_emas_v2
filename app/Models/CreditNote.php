<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CreditNoteItem;

class CreditNote extends Model
{
    use HasFactory;

    protected $table = 'credit_notes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'invoice_no',
        'credit_note_no',
        'date',
        'note',
        'reason',
        'einvoice_version',
        'original_invoice_uuid',
        'type_code', 
        'signed_info', 
        'signature_value', 
        'x509_certificate', 
        'issuer_name', 
        'serial_number', 
        'status',
        'created_by',
        'updated_by',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_no', 'invoice_no');
    }

    public function creditItems()
    {
        return $this->hasMany(CreditNoteItem::class);
    }
} 