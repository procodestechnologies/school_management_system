<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('zkteco-adms.table_prefix', 'zkteco_');
        $table = $prefix.'device_commands';

        // A base64-encoded photo pushed to a device (BIOPHOTO command) can
        // exceed a standard TEXT column's 64KB limit; MEDIUMTEXT holds up
        // to 16MB, comfortably covering even an unresized photo. Raw SQL
        // avoids a doctrine/dbal dependency the app doesn't otherwise need.
        //
        // MySQL-only: SQLite (used in tests) has no separate MEDIUMTEXT
        // type - its TEXT columns already store arbitrarily large content,
        // and `MODIFY` isn't valid SQLite syntax in the first place.
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `{$table}` MODIFY `command_content` MEDIUMTEXT NOT NULL");
        }
    }

    public function down(): void
    {
        $prefix = config('zkteco-adms.table_prefix', 'zkteco_');
        $table = $prefix.'device_commands';

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `{$table}` MODIFY `command_content` TEXT NOT NULL");
        }
    }
};
