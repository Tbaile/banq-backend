<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \App\Http\Resources\UserResource
     */
    public function store(StoreUserRequest $request): UserResource
    {
        $user = User::make($request->only('name', 'email'));
        $user->password = Hash::make($request->input('password'));
        $user->save();
        $user->refresh();

        return new UserResource($user);
    }
}
