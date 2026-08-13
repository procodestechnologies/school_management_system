<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Fees and payroll can't stand alone on a device: a fee row points at a
 * student, a student at a parent record and an institution, an institution
 * at a curriculum. Without these the offline screens render blank names
 * and inserts fail on foreign keys, so the whole graph a device needs has
 * to carry a sync identity too.
 */
return new class extends Migration
{
    /**
     * @var string[]
     */
    private array $tables = [
        'curricula',
        'users',
        'parent_details',
        'institutions',
        'student_details',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->char('tether_id', 26)->nullable()->unique()->after('id');
            });

            DB::table($table)->orderBy('id')->chunkById(500, function ($rows) use ($table) {
                foreach ($rows as $row) {
                    DB::table($table)->where('id', $row->id)->update([
                        'tether_id' => (string) Str::ulid(),
                    ]);
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->dropUnique($table.'_tether_id_unique');
                $blueprint->dropColumn('tether_id');
            });
        }
    }
};
