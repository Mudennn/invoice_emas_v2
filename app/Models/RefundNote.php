<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\RefundNoteItem;

class RefundNote extends Model
{
    use HasFactory;

    protected $table = 'refund_notes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'invoice_no',
        'refund_note_no',
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

    public function refundItems()
    {
        return $this->hasMany(RefundNoteItem::class);
    }

    // Relationship with Einvoices (polymorphic)
    public function einvoices(): MorphMany
    {
        return $this->morphMany(Einvoice::class, 'documentable');
    }
} 