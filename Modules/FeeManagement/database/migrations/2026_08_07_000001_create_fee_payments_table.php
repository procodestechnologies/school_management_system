<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Individual payment events against a Fee - an audit trail the plain
     * running Fee.amount_paid total never had. Recorded either manually or
     * via the AI receipt-scanning flow (source + the raw Gemini response
     * are kept for the latter, so a misread can always be traced back to
     * what the model actually returned).
     */
    public function up(): void
    {
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('reference')->nullable();
            $table->string('payment_method')->nullable();
            $table->date('paid_at')->nullable();
            $table->string('receipt_path')->nullable();
            $table->enum('source', ['manual', 'ai_receipt'])->default('manual');
            $table->json('gemini_raw_response')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};
