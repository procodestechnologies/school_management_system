<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Report cards are delivered to parents as a one-time download link
     * (emailed and texted) rather than an attachment, so each one needs an
     * unguessable token and a record of when it was spent.
     */
    public function up(): void
    {
        Schema::table('report_cards', function (Blueprint $table) {
            $table->string('download_token', 64)->nullable()->unique()->after('pdf_path');
            $table->timestamp('downloaded_at')->nullable()->after('sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('report_cards', function (Blueprint $table) {
            $table->dropUnique(['download_token']);
            $table->dropColumn(['download_token', 'downloaded_at']);
        });
    }
};
