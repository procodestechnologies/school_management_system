<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Separate from 'modules' (which grants whole nwidart modules): this
     * gates pro-tier capabilities *within* a module a plan already has,
     * e.g. AI receipt scanning inside FeeManagement.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->json('features')->nullable()->after('modules');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('features');
        });
    }
};
