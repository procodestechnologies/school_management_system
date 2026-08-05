<?php

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $topic = '';

    public string $message = '';

    // Honeypot: real visitors never fill this hidden field.
    public string $companyWebsite = '';

    public bool $sent = false;

    public function submit(): void
    {
        $key = 'contact-form:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('message', 'Too many messages sent — please try again in a minute.');

            return;
        }

        RateLimiter::hit($key, 60);

        if (filled($this->companyWebsite)) {
            $this->resetForm();

            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'topic' => ['required', 'string', 'in:'.implode(',', array_keys(config('contact.topics')))],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $contactMessage = ContactMessage::create([
            ...$validated,
            'ip_address' => request()->ip(),
        ]);

        if ($recipient = config('contact.recipient')) {
            Mail::to($recipient)->send(new ContactMessageReceived($contactMessage));
        }

        $this->resetForm();
    }

    public function sendAnother(): void
    {
        $this->sent = false;
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'email', 'phone', 'topic', 'message', 'companyWebsite']);
        $this->sent = true;
    }
};
?>
<div>
    @if ($sent)
        <div class="flex flex-col items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-500/20 dark:bg-emerald-500/10">
            <div class="flex items-start gap-3">
                <flux:icon icon="check-circle" class="mt-0.5 size-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                <div>
                    <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">Message sent</p>
                    <p class="mt-0.5 text-sm text-emerald-700 dark:text-emerald-400/90">
                        Thanks for reaching out — our team will get back to you.
                    </p>
                </div>
            </div>
            <button type="button" wire:click="sendAnother"
                class="text-sm font-medium text-emerald-700 hover:underline dark:text-emerald-400">
                Send another message
            </button>
        </div>
    @else
        <form wire:submit="submit" class="space-y-6">
            <div class="hidden" aria-hidden="true">
                <label for="company_website">Leave this field empty</label>
                <input type="text" wire:model="companyWebsite" id="company_website" tabindex="-1"
                    autocomplete="off">
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:input wire:model="name" label="Your name" required autofocus placeholder="Jane Wanjiru" />
                <flux:input wire:model="email" label="Email address" type="email" required
                    placeholder="you@example.com" />
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:input wire:model="phone" label="Phone (optional)" placeholder="07xx xxx xxx" />
                <flux:select wire:model="topic" label="Topic">
                    <flux:select.option value="">Choose a topic</flux:select.option>
                    @foreach (config('contact.topics') as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <flux:textarea wire:model="message" label="Message" rows="5" required
                placeholder="Tell us a bit about your school and what you need..." />

            <flux:button type="submit" variant="primary" class="w-full sm:w-auto">
                Send message
            </flux:button>
        </form>
    @endif
</div>
