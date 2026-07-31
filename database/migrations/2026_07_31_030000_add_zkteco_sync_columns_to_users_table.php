<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('zkteco_synced')->default(false)->after('remember_token');
            $table->timestamp('zkteco_synced_at')->nullable()->after('zkteco_synced');
            $table->text('zkteco_sync_error')->nullable()->after('zkteco_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['zkteco_synced', 'zkteco_synced_at', 'zkteco_sync_error']);
        });
    }
};
