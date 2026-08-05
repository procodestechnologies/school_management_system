<?php

namespace App\Services\Chatbot;

use App\Models\ChatbotVerification;
use App\Models\User;
use App\Notifications\ChatbotVerificationCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Student\Models\StudentDetails;

/**
 * Handles everything about proving that whoever is chatting really is the
 * parent on file for a given admission number, before any student data is
 * ever shown: rate-limited lookup, hashed/expiring/single-use codes sent by
 * email, and verification with a capped number of attempts.
 */
class VerificationService
{
    public function isLookupRateLimited(string $ip): bool
    {
        return RateLimiter::tooManyAttempts(
            $this->lookupKey($ip),
            config('chatbot.max_lookups_per_ip')
        );
    }

    /**
     * Looks up a student by admission number. Counts against the per-IP
     * rate limit regardless of whether a match is found, since scanning
     * for valid admission numbers is exactly the behavior being throttled.
     */
    public function findStudent(string $admissionNumber, string $ip): ?StudentDetails
    {
        RateLimiter::hit($this->lookupKey($ip), config('chatbot.lookup_window_minutes') * 60);

        return StudentDetails::where('admission_number', trim($admissionNumber))->first();
    }

    /**
     * The email a verification code would be sent to for this student, or
     * null if none is on file. Prefers the linked parent account's email
     * (most authoritative, if a parent has a registered account); falls
     * back to the free-text guardian_email column otherwise, which works
     * even when no parent/guardian has an account at all. Note: despite
     * appearing in StudentDetails::$fillable, `parent_email`/`parent_phone`/
     * `parent_name` are not real columns in this schema (no migration ever
     * created them) - only `guardian_*` columns actually exist.
     */
    public function resolveEmail(StudentDetails $student): ?string
    {
        if ($student->parent_id) {
            $email = User::find($student->parent_id)?->email;

            if (filled($email)) {
                return $email;
            }
        }

        if (filled($student->guardian_email)) {
            return $student->guardian_email;
        }

        return null;
    }

    /**
     * @return 'sent'|'rate_limited'
     */
    public function sendCode(StudentDetails $student, string $email, string $command): string
    {
        $sendKey = $this->sendKey($student->admission_number);

        if (RateLimiter::tooManyAttempts($sendKey, config('chatbot.max_sends_per_admission_number'))) {
            return 'rate_limited';
        }

        RateLimiter::hit($sendKey, config('chatbot.send_window_minutes') * 60);

        $length = config('chatbot.otp_length');
        $code = (string) random_int(10 ** ($length - 1), (10 ** $length) - 1);

        ChatbotVerification::create([
            'admission_number' => $student->admission_number,
            'destination_email' => $email,
            'code_hash' => Hash::make($code),
            'command' => $command,
            'expires_at' => now()->addMinutes(config('chatbot.otp_expiry_minutes')),
        ]);

        Notification::route('mail', $email)->notify(new ChatbotVerificationCode($code));

        return 'sent';
    }

    /**
     * @return ChatbotVerification|'no_pending_code'|'expired'|'too_many_attempts'|'invalid'
     */
    public function verify(string $admissionNumber, string $code): ChatbotVerification|string
    {
        $verification = ChatbotVerification::where('admission_number', trim($admissionNumber))
            ->whereNull('consumed_at')
            ->orderByDesc('id')
            ->first();

        if (! $verification) {
            return 'no_pending_code';
        }

        if ($verification->isExpired()) {
            return 'expired';
        }

        if ($verification->attempts >= config('chatbot.max_attempts_per_code')) {
            return 'too_many_attempts';
        }

        if (! Hash::check($code, $verification->code_hash)) {
            $verification->increment('attempts');

            return 'invalid';
        }

        $verification->update(['consumed_at' => now()]);

        return $verification;
    }

    public function maskEmail(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');

        $visible = mb_substr($name, 0, 1);

        return $visible.str_repeat('*', max(mb_strlen($name) - 1, 3)).'@'.$domain;
    }

    private function lookupKey(string $ip): string
    {
        return 'chatbot-lookup:'.$ip;
    }

    private function sendKey(string $admissionNumber): string
    {
        return 'chatbot-send:'.$admissionNumber;
    }
}
