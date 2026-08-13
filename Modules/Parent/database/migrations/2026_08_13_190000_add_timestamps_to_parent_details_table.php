<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * parent_details was created without timestamps, while the model expects
 * them - inserts were writing created_at/updated_at at columns that don't
 * exist. It also makes the table impossible to sync: Tether tracks what a
 * device has already seen using updated_at, so a table without one can
 * never report a change.
 *
 * Existing rows are backfilled so they're included in a device's first
 * pull rather than being invisible to it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parent_details', function (Blueprint $table) {
            $table->timestamps();
        });

        DB::table('parent_details')->whereNull('created_at')->update([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('parent_details', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
