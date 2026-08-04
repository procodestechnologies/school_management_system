<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE lessons MODIFY status ENUM('attended', 'not_attended', 'recovered') DEFAULT 'not_attended'");
    }

    public function down(): void
    {
        DB::statement("UPDATE lessons SET status = 'not_attended' WHERE status = 'recovered'");
        DB::statement("ALTER TABLE lessons MODIFY status ENUM('attended', 'not_attended') DEFAULT 'not_attended'");
    }
};
