<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['required']
        ]);

        $user = User::where('email', $request->email)->first();

        if(! Hash::check($request->password, optional($user)->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')]
            ]);
        }

        return response()->json([
            'plain-text-token' => $user->createToken($request->device_name)->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
