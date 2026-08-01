<?php

namespace App\Services;

class SmsService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('sms');
    }

    public function send(int $mobile, string $message, $provider = null)
    {

        // Use default provider if none specified
        if ($provider === null) {
            $provider = $this->config['default'];
        }

        // Get provider configuration
        $providerConfig = $this->config['providers'][$provider] ?? null;

        if (! $providerConfig) {
            return ['success' => false, 'error' => "SMS provider '{$provider}' not found in configuration"];
        }

        $mobile = $this->formatMobile($mobile);

        // Build POST data using config values with fallbacks
        $postData = http_build_query([
            'userid' => env('PINNACLE_USER_ID'),
            'password' => env('PINNACLE_API_PASSWORD'),
            'mobile' => $mobile,
            'msg' => $message,
            'senderid' => env('PINNACLE_SENDER_ID'),
            'msgType' => env('PINNACLE_MESSAGE_TYPE'),
            'duplicatecheck' => true,
            'output' => env('PINNACLE_RESPONSE'),
            'sendMethod' => env('PINNACLE_SEND_METHOD'),
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $providerConfig['url'] ?? 'https://smsportal.hostpinnacle.co.ke/SMSApi/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_TIMEOUT => $this->config['options']['timeout'] ?? 30,
            CURLOPT_MAXREDIRS => $this->config['options']['max_redirects'] ?? 10,
            CURLOPT_HTTPHEADER => [
                'apikey: '.($providerConfig['apikey'] ?? ''),
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($error) {
            return ['success' => false, 'error' => $error, 'http_code' => $httpCode];
        }
        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'error' => 'Invalid JSON response', 'raw_response' => $response];
        }

        return $decodedResponse;
    }

    private function formatMobile($mobile)
    {
        $mobile = preg_replace('/[^0-9]/', '', $mobile);

        if (strlen($mobile) == 10 && substr($mobile, 0, 1) == '0') {
            return '254'.substr($mobile, 1);
        }

        return $mobile;
    }
}
