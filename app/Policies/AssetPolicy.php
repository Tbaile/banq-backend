<?php

namespace App\Policies;

class AssetPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return true;
    }
}