<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomVerificationEmail;

class CustomVerificationController extends Controller
{
    public function verify($userId, $token)
    {
        // Find the user by ID
        $user = User::findOrFail($userId);

        // Check if the token matches the generated token
        $expectedToken = sha1($user->email . $user->created_at);

        if ($token === $expectedToken) {
            // Mark the user's email as verified
            $user->email_verified_at = now();
            $user->save();

            // Redirect to a success page or login page
            return redirect()->to('/login?success=Email successfully verified!');
        }

        // Handle invalid token case
        return redirect()->to('/login?error=Invalid verification token.');
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return redirect()->route('dashboard')->with('success', 'Your email is already verified.');
        }

        $verificationUrl = $user->generateVerificationUrl();

        Mail::to($user->email)->send(new CustomVerificationEmail($verificationUrl));

        return back()->with('success', 'Verification email resent successfully.');
    }

}
