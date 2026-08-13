<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A payment recorded on a device with no connection and pushed later is a
 * third provenance alongside manual entry and AI receipt scanning. Keeping
 * it distinct means "where did this money come from?" stays answerable
 * after the fact, rather than offline entries masquerading as manual ones.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->enum('source', ['manual', 'ai_receipt', 'offline_sync'])
                ->default('manual')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->enum('source', ['manual', 'ai_receipt'])
                ->default('manual')
                ->change();
        });
    }
};
