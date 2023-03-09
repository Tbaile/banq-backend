<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Tag extends Model
{
    use HasFactory;

    /**
     * Allows laravel to associate tags to every model is needed.
     */
    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }
}
