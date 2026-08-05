<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Curriculum moves from a shared global list to one owned per
     * institution. `institutions.curriculum` is still present at this point
     * (it's dropped in the next migration), so we read it here to clone
     * each referenced curriculum into an institution-owned row rather than
     * repointing it - the old relationship was many institutions to one
     * shared curriculum, so repointing would silently orphan every
     * institution but the last one to "claim" a shared row.
     */
    public function up(): void
    {
        Schema::table('curricula', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('id')
                ->constrained('institutions')->cascadeOnDelete();
        });

        $institutions = DB::table('institutions')->whereNotNull('curriculum')->get(['id', 'curriculum']);

        foreach ($institutions as $institution) {
            $source = DB::table('curricula')->find($institution->curriculum);

            if (! $source) {
                continue;
            }

            DB::table('curricula')->insert([
                'institution_id' => $institution->id,
                'name' => $source->name,
                'status' => $source->status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // The old global rows are now fully superseded by their per-
        // institution clones, but institutions.curriculum still points at
        // them until the next migration drops that column - deleting them
        // here would cascade-delete every institution still referencing
        // one. That cleanup happens in the next migration instead, once
        // it's actually safe.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curricula', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
        });
    }
};
