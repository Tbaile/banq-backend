<?php

namespace App\Models;

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
        'date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'datetime',
    ];

    /**
     * Returns the source asset for the transaction.
     *
     * @return BelongsTo<Asset>
     */
    public function sourceAsset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'source_asset_id');
    }

    /**
     * Returns the destination asset for the transaction.
     *
     * @return BelongsTo<Asset>
     */
    public function destinationAsset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'destination_asset_id');
    }
}
