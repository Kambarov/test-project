<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function generateToken(string $email, string $password, string $userAgent): string|ValidationException
    {
        $user = User::query()
            ->firstWhere('email', $email);

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

        return $user->createToken($userAgent)->plainTextToken;
    }
}
