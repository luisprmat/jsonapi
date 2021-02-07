<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;

class TokenResponse implements Responsable
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toResponse($request)
    {
        return response()->json([
            'plain-text-token' => $this->user->createToken(
                $request->device_name,
                // $this->user->permissions->pluck('name')->toArray()
            )->plainTextToken
        ]);
    }
}
