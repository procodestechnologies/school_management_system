<?php

namespace Modules\FeeManagement\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class FeeReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection  $fees  the parent's outstanding fees (each with its own ->student, ->balance, ->status)
     */
    public function __construct(
        public User $parent,
        public Collection $fees,
    ) {}

    public function build()
    {
        return $this
            ->subject('Pending Fees Reminder')
            ->view('feemanagement::emails.fee-reminder')
            ->with([
                'parent' => $this->parent,
                'fees' => $this->fees,
            ]);
    }
}
