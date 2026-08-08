<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Sends a receipt image to Gemini and gets back structured payment
 * details. A plain REST call - Gemini's generateContent endpoint takes an
 * inline base64 image plus a text prompt, no SDK needed.
 */
class GeminiService
{
    private const BASE_URL = 'https://generativelanguage.googleapis.com/v1beta/models';

    private string $apiKey;

    private string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.api_key');
        $this->model = (string) config('services.gemini.model', 'gemini-2.0-flash');
    }

    /**
     * @return array{amount: ?float, currency: ?string, admission_number: ?string, student_name: ?string, paid_at: ?string, reference: ?string, payment_method: ?string, raw: array}
     */
    public function extractReceiptDetails(string $absoluteImagePath, string $mimeType): array
    {
        $image = base64_encode(file_get_contents($absoluteImagePath));

        $prompt = <<<'PROMPT'
            You are reading a school fee payment receipt (e.g. M-Pesa, bank
            deposit slip, or a printed receipt). Extract these fields and
            respond with ONLY a JSON object, no other text:

            {
              "amount": <number, the amount paid, or null if not found>,
              "currency": <string, e.g. "KES", or null>,
              "admission_number": <string, the student's admission/student
                number if printed anywhere on the receipt (often labelled
                "Account", "Ref", "A/C No", "Admission No", or similar), or
                null if none is present>,
              "student_name": <string, the student's or payer's name if
                present, or null>,
              "paid_at": <string in YYYY-MM-DD format, the payment date, or
                null if not found>,
              "reference": <string, the transaction/receipt reference code,
                or null>,
              "payment_method": <string, one of "mobile_money", "bank",
                "cash", "cheque", "other", based on what the receipt looks
                like, or null if unclear>
            }

            If a field genuinely isn't present or legible, use null - never
            guess or invent a value.
            PROMPT;

        $response = Http::timeout(30)
            ->post(self::BASE_URL."/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [[
                    'parts' => [
                        ['text' => $prompt],
                        ['inline_data' => ['mime_type' => $mimeType, 'data' => $image]],
                    ],
                ]],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                ],
            ])
            ->throw()
            ->json();

        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (! $text) {
            throw new RuntimeException('Gemini returned no readable content for this receipt.');
        }

        $parsed = json_decode($text, true);

        if (! is_array($parsed)) {
            throw new RuntimeException('Gemini returned a response that could not be parsed as JSON.');
        }

        return [
            'amount' => is_numeric($parsed['amount'] ?? null) ? (float) $parsed['amount'] : null,
            'currency' => $parsed['currency'] ?? null,
            'admission_number' => $parsed['admission_number'] ?? null,
            'student_name' => $parsed['student_name'] ?? null,
            'paid_at' => $parsed['paid_at'] ?? null,
            'reference' => $parsed['reference'] ?? null,
            'payment_method' => $parsed['payment_method'] ?? null,
            'raw' => $response,
        ];
    }
}
