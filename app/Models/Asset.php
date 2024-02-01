<?php

namespace App\Models;

use App\Enum\CurrencyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'currency',
    ];

    protected $casts = [
        'currency' => CurrencyEnum::class,
    ];

    /**
     * User that the Asset is attached to.
     *
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Transaction>
     */
    public function income(): HasMany
    {
        return $this->hasMany(Transaction::class, 'destination_asset_id');
    }

    /**
     * @return HasMany<Transaction>
     */
    public function outcome(): HasMany
    {
        return $this->hasMany(Transaction::class, 'source_asset_id');
    }

    /**
     * Load the sum of the given column for the relation.
     *
     * @return float
     */
    public function balance(): float
    {
        $this->loadSum('income', 'amount')->loadSum('outcome', 'amount');
        return $this->income_sum_amount - $this->outcome_sum_amount;
    }
}
