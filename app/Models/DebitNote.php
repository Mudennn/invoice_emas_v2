<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\DebitNoteItem;

class DebitNote extends Model
{
    use HasFactory;

    protected $table = 'debit_notes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'invoice_no',
        'debit_note_no',
        'date',
        'note',
        'status',
        'reason',
        'einvoice_version',
        'original_invoice_uuid',
        'type_code', 
        'signed_info', 
        'signature_value', 
        'x509_certificate', 
        'issuer_name', 
        'serial_number', 
        'created_by',
        'updated_by',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_no', 'invoice_no');
    }

    public function debitItems()
    {
        return $this->hasMany(DebitNoteItem::class);
    }
} 