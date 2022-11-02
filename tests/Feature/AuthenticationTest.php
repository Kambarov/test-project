<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_register()
    {
        $user = User::factory()
            ->make();

        $response = $this->postJson('api/auth/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'password_confirmation' => $user->password
        ]);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('access_token')
        )->assertOk();
    }

    public function test_login()
    {
        $user = User::factory()
            ->create();

        $response = $this->postJson('api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('access_token')
        )->assertOk();
    }

    public function test_login_email_not_verified_yet()
    {
        $user = User::factory()
            ->state([
                'email_verified_at' => null
            ])
            ->create();

        $response = $this->postJson('api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertInvalid([
            'email' => trans('auth.verify_first')
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_login_failed()
    {
        $user = User::factory()
            ->create();

        $response = $this->postJson('api/auth/login', [
            'email' => $user->email,
            'password' => Str::random(8)
        ]);

        $response->assertInvalid([
            'email' => trans('auth.failed')
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_logout()
    {
        $user = User::factory()
            ->create();

        $response = $this->actingAs($user)
            ->postJson('api/auth/logout');

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_get_me()
    {
        $user = User::factory()
            ->create();

        $response = $this->actingAs($user)
            ->getJson('api/auth/me');

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email'
            ]
        ])->assertOk();
    }
}
