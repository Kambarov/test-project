<?php

namespace App\Services;

use App\Mail\Auth\ResendVerifyEmail;
use App\Mail\Auth\WelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function generateToken(string $email, string $password, string $userAgent): string|ValidationException
    {
        $user = User::query()
            ->firstWhere('email', $email);


        if (is_null($user->email_verified_at)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.verify_first')],
            ]);
        }

        if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        return $user->createToken($userAgent)->plainTextToken;
    }

    public function deleteToken(User $user): int
    {
        return $user->tokens()->delete();
    }

    public function register(array $attributes, string $userAgent)
    {
        $user = User::query()
            ->create($attributes);

        $url = $this->generateTempUrl($user);

        Mail::to($user->email)->send(new WelcomeMail($user, $url));

        return $user->createToken($userAgent)->plainTextToken;
    }

    public function verifyEmail(string $token): bool|int
    {
        return User::query()
            ->firstWhere('token', $token)
            ->update([
                'email_verified_at' => now()
            ]);
    }

    public function resendVerifyEmail(string $email): void
    {
        $user = User::query()
            ->firstWhere('email', $email);

        $url = $this->generateTempUrl($user);

        Mail::to($email)->send(new ResendVerifyEmail($user, $url));
    }

    private function generateTempUrl(User $user): string
    {
        $user->updateQuietly([
            'token' => Str::random(32),
            'token_expiry_time' => now()->addHours(2)
        ]);

        return route('verify', $user->token);
    }

    public function isActiveToken(string $token): bool
    {
        return User::query()
            ->where('token', $token)
            ->where('token_expiry_time', '>', now())
            ->exists();
    }
}
