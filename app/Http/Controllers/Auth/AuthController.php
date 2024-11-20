<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\BadRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthPasswordUpdateFormRequest;
use App\Http\Requests\Auth\AuthProfileUpdateFormRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginFormRequest;
use App\Http\Requests\Auth\RegisterFormRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\IdOnlyFormRequest;
use App\Models\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Swift_TransportException;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;


class AuthController extends Controller
{
    public function login(LoginFormRequest $request)
    {
        // dd($request);
        $payload = $request->validated();

        $payload['credentials'] = [
            'email' => $payload['email'],
            'password' => $payload['password'],
        ];

        return DB::transaction(function () use ($payload) {
            $token = null;

            if (!Auth::attempt($payload['credentials'])) {
                return self::errorResponse('Credentials not match');
            }

            $user = auth()->user();
            $token = $user->createToken('personal-token', expiresAt:now()->addHour());

            return self::successResponse('Success', [
                'token' => $token->plainTextToken,
                'user' => auth()->user(),
            ])->header('Authorization', $token->plainTextToken);
        });
    }

    public function logout()
    {
        $result = auth()->user()->currentAccessToken()->delete();

        return self::successResponse('Success', $result);
    }

    public function register(RegisterFormRequest $request){
        $payload = $request->validated();

        if (User::where('email', $payload['email'])->exists())
		{
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
                'isAdmin' => false,
			]);

			// if ($sendEmail && $user->email)
			// {
			// 	self::sendAccountVerifyEmail($user);
			// }

			return $user;
		});

        return self::successResponse('Success', $result);
    }

    public function user()
	{
		$result = auth()->user();
        // dd($result);

		return self::successResponse('Success', [
			'user' => $result,
		]);
	}

    public function forgotPassword(ForgotPasswordRequest $request)
	{
		$payload = $request->validated();

        $result = Password::broker()->sendResetLink(['email' => $payload['email']]);

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
		});

		switch ($result)
		{
			case PasswordBroker::INVALID_TOKEN:
				self::errorResponse('The token is invalid');
				break;
			case PasswordBroker::INVALID_USER:
				self::errorResponse('The user is invalid');
				break;
			default:
				break;
		}

        return self::successResponse('Success', $result);
    }

    public function showResetForm(){
        return view('welcome-vue-web');
    }

}
