<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Tether identifies a row by a client-generated ULID rather than an
 * auto-increment id, so an offline device can create records without
 * coordinating with the server. Existing rows get one backfilled.
 */
return new class extends Migration
{
    /**
     * @var string[]
     */
    private array $tables = [
        'fees',
        'fee_payments',
        'staff_details',
        'staff_payments',
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
