<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite (used in tests) has no native ENUM type - the column is
        // untyped text, so there's nothing to alter there and the app-level
        // cast/validation already accepts 'recovered'.
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE lessons MODIFY status ENUM('attended', 'not_attended', 'recovered') DEFAULT 'not_attended'");
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("UPDATE lessons SET status = 'not_attended' WHERE status = 'recovered'");
        DB::statement("ALTER TABLE lessons MODIFY status ENUM('attended', 'not_attended') DEFAULT 'not_attended'");
    }
};
