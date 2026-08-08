<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Modules\FeeManagement\Models\Fee;
use Modules\FeeManagement\Models\FeePayment;

/**
 * The single place a payment gets applied to a Fee - keeps the
 * FeePayment audit row and Fee.amount_paid (which everything else in the
 * app already reads - dashboards, reminders, the balance/status
 * attributes) in sync, whether the payment was entered manually or via
 * the AI receipt-scanning flow.
 */
class FeePaymentService
{
    public function record(Fee $fee, array $data): FeePayment
    {
        return DB::transaction(function () use ($fee, $data) {
            $payment = FeePayment::create([
                'fee_id' => $fee->id,
                'institution_id' => $fee->institution_id,
                'student_id' => $fee->student_id,
                'amount' => $data['amount'],
                'reference' => $data['reference'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'paid_at' => $data['paid_at'] ?? now(),
                'receipt_path' => $data['receipt_path'] ?? null,
                'source' => $data['source'] ?? 'manual',
                'extraction_raw_response' => $data['extraction_raw_response'] ?? null,
                'recorded_by' => $data['recorded_by'] ?? null,
            ]);

            $fee->increment('amount_paid', $data['amount']);

            return $payment;
        });
    }
}
