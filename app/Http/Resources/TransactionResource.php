<?php

namespace App\Http\Resources;

use App\Enum\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->source_asset_id != null && $this->destination_asset_id != null) {
            $type = TransactionType::TRANSFER;
        } elseif ($this->source_asset_id != null) {
            $type = TransactionType::WITHDRAWAL;
        } else {
            $type = TransactionType::DEPOSIT;
        }

        return [
            'id' => $this->id,
            'description' => $this->description,
            'amount' => $this->amount,
            'source_asset_id' => $this->source_asset_id,
            'destination_asset_id' => $this->destination_asset_id,
            'date' => $this->date->toAtomString(),
            'type' => $type,
        ];
    }
}
