<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Einvoice extends Model
{
    protected $table = 'einvoices';

    protected $fillable = [
        'documentable_id',
        'documentable_type',
        'document_type_code',
        'submission_uid',
        'document_uuid',
        'long_id',
        'status',
        'einvoice_payload',
        'api_response',
        'validation_errors',
        'error_message',
        'retry_count',
        'submitted_at',
        'validated_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'einvoice_payload' => 'array',
        'api_response' => 'array',
        'validation_errors' => 'array',
        'submitted_at' => 'datetime',
        'validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent documentable model (Invoice, CreditNote, DebitNote, RefundNote).
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for pending einvoices
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for submitted einvoices
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope for valid einvoices
     */
    public function scopeValid($query)
    {
        return $query->where('status', 'valid');
    }

    /**
     * Scope for failed einvoices (invalid, rejected, error)
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['invalid', 'rejected', 'error']);
    }

    /**
     * Check if einvoice can be retried
     */
    public function canRetry(): bool
    {
        return $this->retry_count < 5 && in_array($this->status, ['error', 'pending']);
    }

    /**
     * Increment retry count
     */
    public function incrementRetry(): void
    {
        $this->increment('retry_count');
    }
}
