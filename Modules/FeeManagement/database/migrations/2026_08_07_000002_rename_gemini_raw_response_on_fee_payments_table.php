<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extraction switched from Gemini to local OCR - the column holding
     * the raw extraction output is renamed to match, since it's no
     * longer always a Gemini response.
     */
    public function up(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->renameColumn('gemini_raw_response', 'extraction_raw_response');
        });
    }

    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->renameColumn('extraction_raw_response', 'gemini_raw_response');
        });
    }
};
