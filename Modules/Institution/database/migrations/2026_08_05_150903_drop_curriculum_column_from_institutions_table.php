<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('curriculum');
        });

        // Now safe to remove: nothing references these rows any more, now
        // that institutions.curriculum is gone. They were already
        // superseded by per-institution clones in the previous migration.
        DB::table('curricula')->whereNull('institution_id')->delete();

        // Every remaining row now has an institution_id - safe to require
        // it going forward, matching subjects.institution_id's pattern. No
        // doctrine/dbal dependency in this project, so this is a raw ALTER
        // rather than ->change(). MySQL-only: `MODIFY` isn't valid SQLite
        // syntax (used in tests), and SQLite has no NOT NULL enforcement to
        // add here anyway since the app-level FK is already required above.
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE curricula MODIFY institution_id BIGINT UNSIGNED NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE curricula MODIFY institution_id BIGINT UNSIGNED NULL');
        }

        Schema::table('institutions', function (Blueprint $table) {
            // Nullable on rollback - the original many-to-one mapping data
            // was destroyed by the forward migration and can't be restored.
            $table->foreignId('curriculum')->nullable()->after('education_level')
                ->constrained('curricula')->cascadeOnDelete();
        });
    }
};
