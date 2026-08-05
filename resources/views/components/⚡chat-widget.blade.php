<?php

use App\Services\Chatbot\StudentDataService;
use App\Services\Chatbot\VerificationService;
use Livewire\Component;
use Modules\Student\Models\StudentDetails;

new class extends Component
{
    public bool $open = false;

    /** menu | awaiting_admission_number | code_sent */
    public string $stage = 'menu';

    public string $input = '';

    public string $code = '';

    public ?string $activeCommand = null;

    public ?string $pendingAdmissionNumber = null;

    public ?string $verifiedAdmissionNumber = null;

    public ?string $verifiedUntil = null;

    /** @var array<int, array{role: string, lines: array<int, string>}> */
    public array $messages = [];

    public function mount(): void
    {
        $this->pushBot(["Hi! I'm the school assistant. Type / to see what I can help with."]);
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function send(): void
    {
        $text = trim($this->input);
        $this->input = '';

        if ($text === '') {
            return;
        }

        if ($this->stage === 'awaiting_admission_number') {
            $this->pushUser($text);
            $this->handleAdmissionNumber($text);

            return;
        }

        if ($text === '/') {
            $this->pushUser($text);
            $this->showMenu();

            return;
        }

        if (str_starts_with($text, '/')) {
            $this->runCommand(substr($text, 1), announce: true);

            return;
        }

        $this->pushUser($text);
        $this->pushBot(["I didn't quite catch that — type / to see what I can help with."]);
    }

    public function selectCommand(string $command): void
    {
        $this->runCommand($command, announce: true);
    }

    public function submitCode(): void
    {
        $code = trim($this->code);
        $this->code = '';

        if ($code === '' || ! $this->pendingAdmissionNumber) {
            return;
        }

        $this->pushUser(str_repeat('•', strlen($code)));

        $result = app(VerificationService::class)->verify($this->pendingAdmissionNumber, $code);

        if ($result === 'invalid') {
            $this->pushBot(["That code doesn't look right. Please try again."]);

            return;
        }

        if ($result === 'expired') {
            $this->pushBot(['That code has expired. Type / to request a new one.']);
            $this->showMenu();

            return;
        }

        if ($result === 'too_many_attempts') {
            $this->pushBot(['Too many incorrect attempts. Type / to request a new code.']);
            $this->showMenu();

            return;
        }

        if ($result === 'no_pending_code') {
            $this->pushBot(["I don't have a pending code for that. Type / to start again."]);
            $this->showMenu();

            return;
        }

        $this->verifiedAdmissionNumber = $this->pendingAdmissionNumber;
        $this->verifiedUntil = now()->addMinutes(15)->toIso8601String();
        $this->pendingAdmissionNumber = null;

        $this->answerVerifiedCommand();
    }

    public function startOver(): void
    {
        $this->stage = 'menu';
        $this->pendingAdmissionNumber = null;
        $this->activeCommand = null;
        $this->code = '';
        $this->pushBot(['No problem — type / to see what I can help with.']);
    }

    private function runCommand(string $command, bool $announce): void
    {
        $command = strtolower(trim($command, '/'));

        if ($announce) {
            $this->pushUser('/'.$command);
        }

        $definition = config("chatbot.commands.{$command}");

        if (! $definition) {
            $this->pushBot(["I don't recognize that command. Type / to see the list."]);

            return;
        }

        if (! $definition['verified']) {
            $answer = $definition['answer'];
            $this->pushBot(is_array($answer) ? $answer : [$answer]);

            return;
        }

        $this->activeCommand = $command;

        if ($this->hasActiveVerification()) {
            $this->answerVerifiedCommand();

            return;
        }

        $this->stage = 'awaiting_admission_number';
        $this->pushBot(["Sure — what's your child's admission number?"]);
    }

    private function handleAdmissionNumber(string $admissionNumber): void
    {
        $ip = request()->ip() ?? 'unknown';
        $service = app(VerificationService::class);

        if ($service->isLookupRateLimited($ip)) {
            $this->pushBot(["You've made too many attempts. Please wait a bit and try again."]);
            $this->showMenu();

            return;
        }

        $student = $service->findStudent($admissionNumber, $ip);

        if (! $student) {
            $this->pushBot(["I couldn't find a student with that admission number. Please double-check and try again, or type / to start over."]);

            return;
        }

        $email = $service->resolveEmail($student);

        if (! $email) {
            $this->pushBot(['We don\'t have a verified email on file for this student. Please contact the school office directly.']);
            $this->showMenu();

            return;
        }

        $result = $service->sendCode($student, $email, (string) $this->activeCommand);

        if ($result === 'rate_limited') {
            $this->pushBot(['Too many codes have been requested for this student recently. Please try again later.']);
            $this->showMenu();

            return;
        }

        $this->pendingAdmissionNumber = $student->admission_number;
        $this->stage = 'code_sent';
        $masked = $service->maskEmail($email);
        $this->pushBot(["We've sent a 6-digit code to {$masked}. Enter it below to continue."]);
    }

    private function answerVerifiedCommand(): void
    {
        $student = StudentDetails::where('admission_number', $this->verifiedAdmissionNumber)->first();

        if (! $student || ! $this->activeCommand) {
            $this->pushBot(['Something went wrong finding that student — please start again.']);
            $this->showMenu();

            return;
        }

        $lines = app(StudentDataService::class)->answer($this->activeCommand, $student);
        $this->pushBot(array_merge(["You're verified! Here's what you asked for:"], $lines));
        $this->stage = 'menu';
    }

    private function hasActiveVerification(): bool
    {
        return $this->verifiedAdmissionNumber !== null
            && $this->verifiedUntil !== null
            && now()->lessThan($this->verifiedUntil);
    }

    private function showMenu(): void
    {
        $this->stage = 'menu';
        $this->pushBot(["Here's what I can help with:"]);
    }

    private function pushUser(string $text): void
    {
        $this->messages[] = ['role' => 'user', 'lines' => [$text]];
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function pushBot(array $lines): void
    {
        $this->messages[] = ['role' => 'bot', 'lines' => $lines];
    }
};
?>
<div class="fixed bottom-6 right-6 z-50">
    @unless ($open)
        <span class="pointer-events-none absolute inset-0 animate-ping rounded-full bg-indigo-500/40"></span>
        <button wire:click="toggle" type="button"
            class="relative flex size-14 items-center justify-center rounded-full bg-zinc-900 text-white shadow-lg transition hover:scale-105 hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
            aria-label="Open chat assistant">
            <flux:icon icon="chat-bubble-left-right" class="size-6" />
        </button>
    @else
        <div
            class="flex h-[32rem] w-80 flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xl sm:w-96 dark:border-white/10 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-100 px-4 py-3 dark:border-white/10">
                <span class="text-sm font-semibold text-zinc-900 dark:text-white">
                    {{ config('app.name') }} Assistant
                </span>
                <button wire:click="toggle" type="button"
                    class="text-zinc-400 transition hover:text-zinc-600 dark:hover:text-zinc-200" aria-label="Close">
                    <flux:icon icon="x-mark" class="size-5" />
                </button>
            </div>

            <div class="flex-1 space-y-3 overflow-y-auto px-4 py-3" x-data
                x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
                x-effect="$el.scrollTop = $el.scrollHeight">
                @foreach ($messages as $message)
                    <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div
                            class="max-w-[85%] rounded-2xl px-3 py-2 text-sm leading-relaxed {{ $message['role'] === 'user' ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' : 'bg-zinc-100 text-zinc-800 dark:bg-white/10 dark:text-zinc-100' }}">
                            @foreach ($message['lines'] as $line)
                                <p @if (! $loop->first) class="mt-1" @endif>{{ $line }}</p>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($stage === 'code_sent')
                <form wire:submit="submitCode"
                    class="border-t border-zinc-100 px-4 py-3 dark:border-white/10">
                    <flux:otp wire:model="code" length="6" name="code" label="Enter the code we emailed you"
                        label:sr-only class="mx-auto" />
                    <flux:button type="submit" variant="primary" class="mt-3 w-full">Verify</flux:button>
                    <button type="button" wire:click="startOver"
                        class="mt-2 w-full text-center text-xs text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                        Start over
                    </button>
                </form>
            @else
                @if ($stage === 'menu')
                    <div class="flex flex-wrap gap-1.5 border-t border-zinc-100 px-3 py-2 dark:border-white/10">
                        @foreach (config('chatbot.commands') as $key => $definition)
                            <button wire:click="selectCommand('{{ $key }}')" type="button"
                                title="{{ $definition['description'] }}"
                                class="rounded-full border border-zinc-200 px-2.5 py-1 text-xs text-zinc-600 transition hover:bg-zinc-50 dark:border-white/10 dark:text-zinc-300 dark:hover:bg-white/5">
                                /{{ $key }}
                            </button>
                        @endforeach
                    </div>
                @endif

                <form wire:submit="send"
                    class="flex items-center gap-2 border-t border-zinc-100 px-3 py-2 dark:border-white/10">
                    <input type="text" wire:model="input"
                        placeholder="{{ $stage === 'awaiting_admission_number' ? 'Enter admission number…' : 'Type / for commands…' }}"
                        class="flex-1 rounded-full border border-zinc-200 bg-transparent px-3 py-1.5 text-sm text-zinc-900 outline-none focus:border-zinc-400 dark:border-white/10 dark:text-white dark:focus:border-white/30" />
                    <button type="submit"
                        class="flex size-8 shrink-0 items-center justify-center rounded-full bg-zinc-900 text-white transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
                        aria-label="Send">
                        <flux:icon icon="paper-airplane" class="size-4" />
                    </button>
                </form>
            @endif
        </div>
    @endunless
</div>
