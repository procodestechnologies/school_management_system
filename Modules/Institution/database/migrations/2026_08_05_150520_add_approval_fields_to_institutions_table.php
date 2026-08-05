<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            // Defaults to true so existing institutions are grandfathered in
            // as approved - only newly self-created ones start pending.
            $table->boolean('is_approved')->default(true)->after('status');
            $table->timestamp('approved_at')->nullable()->after('is_approved');
            $table->foreignId('approved_by_id')->nullable()->after('approved_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by_id');
            $table->dropColumn(['is_approved', 'approved_at']);
        });
    }
};
