<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChatbotVerificationCode extends Notification
{
    use Queueable;

    public function __construct(private readonly string $code) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your verification code')
            ->greeting('Hello,')
            ->line('Someone requested access to your child\'s school records through the '.config('app.name').' assistant.')
            ->line('Here is your verification code:')
            ->line(new \Illuminate\Support\HtmlString('<p style="font-size:28px;font-weight:600;letter-spacing:0.3em;text-align:center;">'.$this->code.'</p>'))
            ->line('This code expires in 10 minutes and can only be used once.')
            ->line('If you didn\'t request this, you can safely ignore this email.');
    }
}
