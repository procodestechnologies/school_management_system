<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * An enrolled offline client. Distinct from the `devices` table, which is
 * biometric attendance hardware - this is a laptop or phone running the
 * offline app and syncing through Tether.
 *
 * The row outlives its token on purpose: revoking deletes the token but
 * keeps the record, so a lost device stays visible in the audit trail
 * rather than vanishing from the list.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            // The account the device syncs as - what scopes everything it
            // may pull or push.
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->enum('platform', ['desktop', 'android', 'ios'])->default('desktop');
            // The client_id the device sends with every sync request.
            $table->char('client_id', 26)->unique();
            $table->foreignId('token_id')->nullable()->constrained('personal_access_tokens')->nullOnDelete();
            $table->timestamp('last_synced_at')->nullable();
            $table->string('last_seen_ip', 45)->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['institution_id', 'revoked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_devices');
    }
};
