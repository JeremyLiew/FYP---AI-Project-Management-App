<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $report;

    // Constructor to pass report data to the mail
    public function __construct($report)
    {
        $this->report = $report;
    }

    public function build()
    {
        return $this->subject('Your Project Report')
                    ->view('emails.project_report') 
                    ->with(['report' => $this->report])
                    ->to('liewweilun1005@gmail.com');
    }
}

