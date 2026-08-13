<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Where a paired device keeps what it was told at pairing time - server
 * address, its client id, its sync token.
 *
 * These could live in .env, but a packaged NativePHP build has no
 * editable .env once it's on someone's laptop: pairing happens after
 * installation, so the values have to be writable at runtime.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_settings');
    }
};
