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
        // $payload = $request->validated();

        // $result = DB::transaction(function () use ($payload){
        //     {
        //         $newsletter_subscriber = NewsletterSubscriber::create([
        //             'email' => $payload['email'],
        //         ]);
        //     }

        //     $data = [
        //         "email" =>  $payload['email'],
        //     ];

        //     Mail::to($payload['email'])->send(new ContactUs($data));

        //     return $newsletter_subscriber;

        // });


        return self::successResponse("Success", $result);
    }
}
