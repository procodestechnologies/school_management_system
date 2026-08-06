<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Some environments already carry a legacy free-text
        // `subscription_plan` column (predating this migration, never
        // itself created by a tracked migration). Drop it so it can be
        // recreated below as a proper foreign key to `plans`.
        if (Schema::hasColumn('institutions', 'subscription_plan')) {
            Schema::table('institutions', function (Blueprint $table) {
                $table->dropColumn('subscription_plan');
            });
        }

        if (! Schema::hasColumn('institutions', 'subscription_expires_at')) {
            Schema::table('institutions', function (Blueprint $table) {
                $table->timestamp('subscription_expires_at')->nullable();
            });
        }

        Schema::table('institutions', function (Blueprint $table) {
            $table->foreignId('subscription_plan')->nullable()->constrained('plans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subscription_plan');
            $table->dropColumn('subscription_expires_at');
        });
    }
};
