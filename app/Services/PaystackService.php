<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Thin wrapper over Paystack's REST API. No SDK dependency - the surface
 * used here (initialize + verify a transaction, validate webhook
 * signatures) is a handful of plain HTTP calls.
 */
class PaystackService
{
    private const BASE_URL = 'https://api.paystack.co';

    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = (string) config('services.paystack.secret_key');
    }

    public function initializeTransaction(array $data): array
    {
        return Http::withToken($this->secretKey)
            ->baseUrl(self::BASE_URL)
            ->post('/transaction/initialize', $data)
            ->throw()
            ->json();
    }

    public function verifyTransaction(string $reference): array
    {
        return Http::withToken($this->secretKey)
            ->baseUrl(self::BASE_URL)
            ->get('/transaction/verify/'.rawurlencode($reference))
            ->throw()
            ->json();
    }

    public function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        if (! $signature) {
            return false;
        }

        return hash_equals(hash_hmac('sha512', $payload, $this->secretKey), $signature);
    }
}
