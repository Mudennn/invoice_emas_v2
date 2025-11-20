<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('einvoices', function (Blueprint $table) {
            $table->id();

            // Polymorphic relationship
            $table->morphs('documentable');

            // Document identifiers
            $table->string('document_type_code', 2)->comment('01=Invoice, 02=Credit Note, 03=Debit Note, 04=Refund Note');
            $table->string('submission_uid', 26)->nullable()->unique()->comment('MyInvois submission UID');
            $table->string('document_uuid', 26)->nullable()->unique()->comment('MyInvois document UUID');
            $table->string('long_id', 100)->nullable()->unique()->comment('MyInvois long ID after validation');

            // Status tracking
            $table->enum('status', [
                'pending',
                'submitted',
                'valid',
                'invalid',
                'cancelled',
                'rejected',
                'error'
            ])->default('pending');

            // Payload and responses
            $table->json('einvoice_payload')->nullable()->comment('UBL 2.1 JSON payload');
            $table->json('api_response')->nullable()->comment('Full API response from MyInvois');
            $table->json('validation_errors')->nullable()->comment('Validation error details');
            $table->text('error_message')->nullable();

            // Retry tracking
            $table->integer('retry_count')->default(0);

            // Timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Indexes
            $table->index(['documentable_type', 'documentable_id']);
            $table->index('status');
            $table->index('document_type_code');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('einvoices');
    }
};
