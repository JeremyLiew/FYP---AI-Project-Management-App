<?php

namespace App\Http\Controllers\Web;

use App\Mail\ContactUs;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Web\SendMailRequest;

class ContactUsController extends Controller
{
    protected $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    public function sendEmail(SendMailRequest $request)
    {
        $payload = $request->validated();

        $data = [
            'firstName' => $payload['firstName'],
            'lastName' => $payload['lastName'],
            'email' => $payload['email'],
            'subject' => $payload['subject'] ?? 'No Subject',
            'message' => $payload['message'],
        ];

        $this->activityLogger->logActivity(
            'Attempted to send email',
            'ContactUs',
            0,
            [
                'sender' => $data['email'],
                'subject' => $data['subject'],
                'message' => $data['message'],
            ]
        );

        try {
            Mail::to(env('MAIL_USERNAME', 'jliew1114@gmail.com'))->send(new ContactUs($data));

            $this->activityLogger->logActivity(
                'Email sent successfully',
                'ContactUs',
                0,
                [
                    'sender' => $data['email'],
                    'subject' => $data['subject'],
                    'message' => $data['message'],
                ]
            );

            return response()->json(['message' => 'Email sent successfully!'], 200);
        } catch (\Exception $e) {
            $this->activityLogger->logActivity(
                'Failed to send email',
                'ContactUs',
                0,
                [
                    'sender' => $data['email'],
                    'subject' => $data['subject'],
                    'message' => $data['message'],
                    'error' => $e->getMessage(),
                ],
                'error'
            );

            return response()->json(['message' => 'Failed to send email. Please try again later.'], 500);
        }
    }
}
