<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A Director can now own more than one institution. This is which one
     * they're currently "running the system as" - set when they choose it
     * from the institutions index, remembered across logins, and cleared
     * back to null if it's ever lost/transferred (see HasInstitution).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('active_institution_id')->nullable()->after('id')
                ->constrained('institutions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('active_institution_id');
        });
    }
};
