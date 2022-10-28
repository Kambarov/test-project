<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendVerifyEmailRequest;
use App\Http\Resources\Users\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    private AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return response()
            ->json([
                'access_token' => $this->service->generateToken(
                    $request->validated('email'),
                    $request->validated('password'),
                    $request->userAgent()
                )
            ]);
    }

    public function logout(): JsonResponse
    {
        $this->service->deleteToken(auth()->user);

        return response()
            ->json([
                'message' => trans('auth.logged_out')
            ], Response::HTTP_NO_CONTENT);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return response()
            ->json([
                'access_token' => $this->service
                    ->register($request->validated(), $request->userAgent())
            ]);
    }

    public function verifyEmail(string $token): JsonResponse
    {
        $this->service->verifyEmail($token);

        return response()
            ->json([
                'message' => trans('messages.updated')
            ]);
    }

    public function resendVerifyEmail(ResendVerifyEmailRequest $request): JsonResponse
    {
        $this->service->resendVerifyEmail($request->validated('email'));

        return response()
            ->json([
                'message' => trans('app.message_sent')
            ]);
    }

    public function getMe(): UserResource
    {
        return new UserResource(auth()->user());
    }
}
