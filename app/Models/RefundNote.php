<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

    /**
     * Get the latest einvoice record for this refund note.
     */
    public function getLatestEinvoice(): ?Einvoice
    {
        return $this->einvoices()->latest()->first();
    }

    /**
     * Get the current e-invoice status.
     */
    public function getEinvoiceStatus(): string
    {
        $latestEinvoice = $this->getLatestEinvoice();

        return $latestEinvoice ? $latestEinvoice->status : 'not_submitted';
    }

    /**
     * Check if refund note has a valid einvoice.
     */
    public function hasValidEinvoice(): bool
    {
        return $this->einvoices()->where('status', 'valid')->exists();
    }

    /**
     * Check if refund note can be submitted to MyInvois.
     */
    public function canSubmitToMyInvois(): bool
    {
        // Cannot submit if already has valid einvoice
        if ($this->hasValidEinvoice()) {
            return false;
        }

        // Cannot submit if currently being processed
        $latestEinvoice = $this->getLatestEinvoice();
        if ($latestEinvoice && in_array($latestEinvoice->status, ['submitted', 'processing'])) {
            return false;
        }

        return true;
    }
}
