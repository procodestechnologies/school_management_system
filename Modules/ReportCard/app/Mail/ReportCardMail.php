<?php

namespace Modules\ReportCard\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Institution\Models\Institution;
use Modules\ReportCard\Models\ReportCard;

class ReportCardMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ReportCard $reportCard,
        public User $student,
        public Institution $institution,
        public string $downloadUrl,
    ) {}

    public function build()
    {
        return $this
            ->subject("{$this->student->name}'s Report Card - {$this->reportCard->term}")
            ->view('reportcard::emails.report-card')
            ->with([
                'student' => $this->student,
                'institution' => $this->institution,
                'reportCard' => $this->reportCard,
                'downloadUrl' => $this->downloadUrl,
            ]);
    }
}
