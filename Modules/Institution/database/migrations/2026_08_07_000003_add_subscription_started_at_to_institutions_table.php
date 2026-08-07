<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marks the start of the "amount paid so far" window used to work out
     * how much more an institution owes to reach a given plan. It's only
     * reset when a payment is made while the institution has no active
     * plan (first subscription, or renewing after expiry) - a same-window
     * upgrade keeps accumulating against it instead of resetting.
     */
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->timestamp('subscription_started_at')->nullable()->after('subscription_plan');
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn('subscription_started_at');
        });
    }
};
