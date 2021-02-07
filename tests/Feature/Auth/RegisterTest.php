<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_register()
    {
        $response = $this->postJson(route('api.v1.register'), [
            'name' => 'Luis Parrado',
            'email' => 'luisprmat@gmail.com',
            'device_name' => 'iPhone de Luis',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            'The plain text token is invalid'
        );

        $this->assertDatabaseHas('users', [
            'name' => 'Luis Parrado',
            'email' => 'luisprmat@gmail.com',
        ]);
    }

    /** @test */
    function name_is_required()
    {
        $this->postJson(route('api.v1.register'), [
            'name' => '',
            'email' => 'luisprmat@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de Luis'
        ])->assertJsonValidationErrors('name');
    }

    /** @test */
    function email_is_required()
    {
        $this->postJson(route('api.v1.register'), [
            'name' => 'Luis Parrado',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de Luis'
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    function email_must_be_valid()
    {
        $this->postJson(route('api.v1.register'), [
            'name' => 'Luis Parrado',
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de Luis'
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    function email_must_be_unique()
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.register'), [
            'name' => 'Luis Parrado',
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de Luis'
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    function password_is_required()
    {
        $this->postJson(route('api.v1.register'), [
            'name' => 'Luis Parrado',
            'email' => 'luisprmat@gmail.com',
            'password' => '',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de Luis'
        ])->assertJsonValidationErrors('password');
    }

    /** @test */
    function password_must_be_confirmed()
    {
        $this->postJson(route('api.v1.register'), [
            'name' => 'Luis Parrado',
            'email' => 'luisprmat@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'not-confirmed',
            'device_name' => 'iPhone de Luis'
        ])->assertJsonValidationErrors('password');
    }

    /** @test */
    function device_name_is_required()
    {
        $this->postJson(route('api.v1.register'), [
            'name' => 'Luis Parrado',
            'email' => 'luisprmat@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => ''
        ])->assertJsonValidationErrors('device_name');
    }
}
