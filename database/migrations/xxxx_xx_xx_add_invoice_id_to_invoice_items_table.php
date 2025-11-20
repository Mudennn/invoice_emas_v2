<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'invoice_id')) {
                $table->unsignedBigInteger('invoice_id')->after('id')->nullable();
                $table->foreign('invoice_id')
                      ->references('id')
                      ->on('invoices')
                      ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
        });
    }
}; 