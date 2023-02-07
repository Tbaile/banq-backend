<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Register the user.
     *
     * @param  \App\Http\Requests\RegisterRequest  $request
     * @return \App\Http\Resources\UserResource
     */
    public function __invoke(RegisterRequest $request): UserResource
    {
        $user = User::make($request->only('name', 'email'));
        $user->password = Hash::make($request->input('password'));
        $user->save();
        $user->refresh();

        return new UserResource($user);
    }
}
