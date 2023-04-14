<?php

namespace App\Http\Requests;

use App\Enum\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'type' => [
                'required',
                new Enum(TransactionType::class),
            ],
            'amount' => 'required|decimal:0,2|min:0.01',
            'source_asset_id' => [
                'exclude_if:type,' . TransactionType::DEPOSIT->value,
                'required',
                'numeric',
                'exists:App\Models\Asset,id',
            ],
            'destination_asset_id' => [
                'exclude_if:type,' . TransactionType::WITHDRAWAL->value,
                'required',
                'numeric',
                'exists:App\Models\Asset,id',
            ],
        ];
    }
}
