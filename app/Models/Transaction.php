<?php

namespace App\Models;

use App\Enum\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'type',
        'amount',
        'source_asset_id',
    ];

    protected $casts = [
        'type' => TransactionType::class,
    ];

    /**
     * Returns the source asset for the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Asset>
     */
    public function sourceAsset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'source_asset_id');
    }
}
