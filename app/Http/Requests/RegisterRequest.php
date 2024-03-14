<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Knuckles\Scribe\Attributes\BodyParam;

#[BodyParam(name: 'name', description: 'Name of the user', required: true, example: 'John Doe')]
#[BodyParam(name: 'email', description: 'Email of the user', required: true, example: 'john.doe@example.com')]
#[BodyParam(name: 'password', description: 'Password of the user', required: true, example: 'password')]
class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:App\Models\User,email',
            'password' => 'required',
        ];
    }
}
