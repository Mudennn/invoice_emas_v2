<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // public function up(): void
    // {
    //     Schema::create('credit_notes', function (Blueprint $table) {
    //         $table->id();
    //         $table->string('credit_note_number')->unique();
    //         $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
    //         $table->date('date');
    //         $table->decimal('amount', 10, 2);
    //         $table->string('reason');
    //         $table->text('notes')->nullable();
    //         $table->string('status');
    //         $table->timestamps();
    //     });
    // }

    // public function down(): void
    // {
    //     Schema::dropIfExists('credit_notes');
    // }
}; 