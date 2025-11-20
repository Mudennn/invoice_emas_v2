<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // public function up(): void
    // {
    //     Schema::create('credit_items', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('credit_note_id')->constrained()->onDelete('cascade');
    //         $table->foreignId('invoice_item_id')->constrained('invoice_items')->onDelete('cascade');
    //         $table->decimal('amount', 10, 2);
    //         $table->string('description');
    //         $table->timestamps();
    //     });
    // }

    // public function down(): void
    // {
    //     Schema::dropIfExists('credit_items');
    // }
}; 