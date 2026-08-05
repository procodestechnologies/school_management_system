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
        Schema::create('chatbot_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('admission_number');
            $table->string('destination_email');
            $table->string('code_hash');
            $table->string('command')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            // dateTime, not timestamp: a non-nullable `timestamp` column
            // with no explicit default is treated by MySQL (when
            // explicit_defaults_for_timestamp is off, as it is here) as the
            // legacy "first TIMESTAMP column" and gets an implicit
            // `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP" -
            // silently resetting expires_at to "now" on every unrelated
            // update to the row (e.g. incrementing attempts), which would
            // make every code expire the instant someone mistypes it once.
            $table->dateTime('expires_at');
            $table->dateTime('consumed_at')->nullable();
            $table->timestamps();

            $table->index('admission_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_verifications');
    }
};
