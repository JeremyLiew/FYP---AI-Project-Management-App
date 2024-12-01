<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\SendMailRequest;
use App\Mail\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\DB;

class ContactUsController extends Controller
{
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

        // Send email to your address
        Mail::to(env('MAIL_USERNAME'))->send(new ContactUs($data));


        return response()->json(['message' => 'Email sent successfully!'], 200);
    }
}
