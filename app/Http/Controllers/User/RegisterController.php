<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\Unauthenticated;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    #[Group('User')]
    #[Unauthenticated]
    #[ResponseFromApiResource(UserResource::class, User::class, Response::HTTP_CREATED)]
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $user = User::make($request->only('name', 'email'));
        $user->password = Hash::make($request->input('password'));
        $user->save();
        $user->refresh();

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
