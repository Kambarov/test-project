<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ResendVerifyEmailRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email'
        ];
    }

    protected function prepareForValidation()
    {
        $user = User::query()
            ->firstWhere('email', $this->email);

        if (!is_null($user->email_verified_at)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.already_verified')],
            ]);
        }
    }
}
