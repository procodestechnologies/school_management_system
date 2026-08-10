<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Email verification is now the signup gate (User implements
     * MustVerifyEmail), so the separate 'pending' account state is gone.
     * Nothing in the app ever moved an account off 'pending', so every such
     * row is just a normal account that was never activatable.
     */
    public function up(): void
    {
        // Grandfather anyone who predates enforcement - verification was
        // never actually required before this, so locking existing users
        // out over an unverified address would be a regression for them.
        DB::table('users')->whereNull('email_verified_at')->update(['email_verified_at' => now()]);

        DB::table('users')->where('status', 'pending')->update(['status' => 'active']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'suspended'])->default('active')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'pending', 'suspended'])->default('pending')->change();
        });
    }
};
