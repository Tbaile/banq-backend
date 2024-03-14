<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Knuckles\Scribe\Attributes\BodyParam;

#[BodyParam('email', description: 'Email used during registration', required: true, example: 'john.doe@example.com')]
#[BodyParam('password', description: 'Password of the user', required: true, example: 'password')]
#[BodyParam('device_name', description: 'Recognizable name of the device logging in.', required: true, example: 'Cool Phone')]
class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string',
            'password' => 'required',
            'device_name' => 'required|string',
        ];
    }
}
