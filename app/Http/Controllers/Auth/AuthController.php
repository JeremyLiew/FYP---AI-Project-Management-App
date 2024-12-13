<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Swift_TransportException;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomVerificationEmail;
use App\Exceptions\BadRequestException;
use App\Http\Requests\IdOnlyFormRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Requests\Auth\LoginFormRequest;
use Illuminate\Auth\Passwords\PasswordBroker;
use Symfony\Component\HttpFoundation\Response;

use App\Http\Requests\Auth\RegisterFormRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\AuthProfileUpdateFormRequest;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Http\Requests\Auth\AuthPasswordUpdateFormRequest;


class AuthController extends Controller
{
    protected $activityLogger;

    public function __construct(ActivityLogger $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    public function login(LoginFormRequest $request)
    {
        $payload = $request->validated();

        $payload['credentials'] = [
            'email' => $payload['email'],
            'password' => $payload['password'],
        ];

        $email = $payload['email'];
        $key = 'login.attempts.' . $email;

        // Check rate limits
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return self::errorResponse(
                "Too many login attempts. Please try again in $seconds seconds.",
                null,
                Response::HTTP_TOO_MANY_REQUESTS
            );
        }

        return DB::transaction(function () use ($payload, $key) {
            $token = null;

            if (!Auth::attempt($payload['credentials'], $payload['remember'] ?? false)) {
                RateLimiter::hit($key, 60);
                $this->activityLogger->logActivity('Failed login attempt', User::class, 0, ['email' => $payload['email']] , 'warning');
                return self::errorResponse('Credentials not match', null, Response::HTTP_UNAUTHORIZED);
            }

            // Clear rate limiter on successful login
            RateLimiter::clear($key);

            $user = auth()->user();
            $token = $user->createToken('personal-token', expiresAt: now()->addHour());

            $this->activityLogger->logActivity('User logged in', User::class, $user->id);

            return self::successResponse('Success', [
                'token' => $token->plainTextToken,
                'user' => auth()->user(),
            ])->header('Authorization', $token->plainTextToken);
        });
    }

    public function logout()
    {
        $user = auth()->user();
        $result = auth()->user()->currentAccessToken()->delete();

        $this->activityLogger->logActivity('User logged out', User::class, $user->id);

        return self::successResponse('Success', $result);
    }

    public function register(RegisterFormRequest $request){
        $payload = $request->validated();

        if (User::where('email', $payload['email'])->exists())
		{
            $this->activityLogger->logActivity('Failed registration attempt - email taken', User::class, 0, ['email' => $payload['email']] , 'warning');
			return self::errorResponse('Email has been taken');
		}

        $result = DB::transaction(function () use ($payload)
		{
			$user = new User();

			$user = $user->create([
				'name' => $payload['name'],
				'email' => $payload['email'],
				'password' => Hash::make($payload['password']),
				'email_verified_at' => $payload['email_verified_at'] ?? null,
                'application_role_id' => 3, // default to normal user
			]);

			// Send custom verification email
            $verificationUrl = $user->generateVerificationUrl();
            Mail::to($user->email)->send(new CustomVerificationEmail($verificationUrl));

            $this->activityLogger->logActivity('New user registered', User::class, $user->id);

			return $user;
		});

        return self::successResponse('Success', $result);
    }

    public function user()
	{
		$result = auth()->user();

        $this->activityLogger->logActivity('User accessed profile', User::class, $result->id);

		return self::successResponse('Success', [
			'user' => $result,
		]);
	}

    public function forgotPassword(ForgotPasswordRequest $request)
	{
		$payload = $request->validated();

        $result = Password::broker()->sendResetLink(['email' => $payload['email']]);

        $this->activityLogger->logActivity(
            $result === Password::RESET_LINK_SENT
                ? 'Password reset email sent'
                : 'Failed to send password reset email',
            User::class,
            0,
            ['email' => $payload['email']]
        );

        return $result === Password::RESET_LINK_SENT
                ? self::successResponse('Success', [
                    'email' => $payload['email'],
                ])
                :  self::errorResponse('Email failed to send');


	}

    public function resetPassword(ResetPasswordRequest $request)
    {
        $payload = $request->validated();


        $result = Password::broker()->reset($payload, function ($user, $password)
		{
			$user->password = Hash::make($password);
			$user->email_verified_at = now();
			$user->save();

            $this->activityLogger->logActivity('Password reset', User::class, $user->id);
		});

		switch ($result)
		{
			case PasswordBroker::INVALID_TOKEN:
                $this->activityLogger->logActivity('Invalid password reset token', User::class, 0);
				self::errorResponse('The token is invalid');
				break;
			case PasswordBroker::INVALID_USER:
                $this->activityLogger->logActivity('Invalid user for password reset', User::class, 0);
				self::errorResponse('The user is invalid');
				break;
			default:
				break;
		}

        return self::successResponse('Success', $result);
    }

    public function showResetForm(){
        $this->activityLogger->logActivity('Viewed password reset form', User::class, 0);
        return view('welcome-vue-web');
    }

}
