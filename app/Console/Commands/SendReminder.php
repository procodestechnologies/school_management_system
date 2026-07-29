<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// #[Signature('app:send-reminder')]
// #[Description('Command description')]
class SendReminder extends Command
{
    protected $signature = "remind:user";
    protected $description = 'Send fee reminder';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $user = User::findOrFail(2);

            Mail::send('mail-template', ['name' => $user->name], function ($message) use ($user) {
                $message->to($user->email)->subject('Hello');
            });

            $this->info('Email sent successfully to ' . $user->email);
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            Log::error('Mail error: ' . $e->getMessage());
        }
    }
}
