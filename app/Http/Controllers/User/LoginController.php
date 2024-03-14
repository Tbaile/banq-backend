<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthTokenResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Unauthenticated;

#[Group('User')]
#[Unauthenticated]
#[Response([
    'data' => [
        'token' => '1|3YHzxPf4xSVTGn1au3QoUF5UnrcppcYWLvAI2wdX66f959c8'
    ]
])]
class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();
        if (is_null($user) || !Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('The provided credentials are incorrect.'),
            ]);
        }

        return (new AuthTokenResource($user->createToken($request->input('device_name'))))
            ->response();
    }
}
