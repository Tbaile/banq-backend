<?php

namespace App\Http\Requests;

use DateTimeInterface;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'amount' => 'required|decimal:0,2|min:0.01',
            'source_asset_id' => [
                'required_without:destination_asset_id',
                'numeric',
                'exists:App\Models\Asset,id',
            ],
            'destination_asset_id' => [
                'required_without:source_asset_id',
                'numeric',
                'exists:App\Models\Asset,id',
            ],
            'date' => 'required|date_format:'.DateTimeInterface::ATOM,
            'latitude' => 'required_with:longitude,address|numeric|between:-90,90',
            'longitude' => 'required_with:latitude,address|numeric|between:-180,180',
            'address' => 'required_with:latitude,longitude|string|max:255',
        ];
    }
}
