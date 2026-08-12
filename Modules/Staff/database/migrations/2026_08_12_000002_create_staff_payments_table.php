<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_details_id')->constrained('staff_details')->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            // Anchored to the first day of the payroll month.
            $table->date('period');
            $table->decimal('gross_amount', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'mobile_money', 'cheque'])->default('bank_transfer');
            $table->string('reference')->nullable();
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // One payslip per staff member per month is enforced in the
            // controller rather than by a unique index - soft-deleted rows
            // would otherwise permanently block re-entering a period.
            $table->index(['staff_details_id', 'period']);
            $table->index(['institution_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_payments');
    }
};
