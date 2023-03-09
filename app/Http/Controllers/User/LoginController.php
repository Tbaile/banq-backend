<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthTokenResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request): AuthTokenResource
    {
        $user = User::where('email', $request->input('email'))->first();
        if (is_null($user) || ! Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('The provided credentials are incorrect.'),
            ]);
        }

        return new AuthTokenResource($user->createToken($request->input('device_name')));
    }
}
