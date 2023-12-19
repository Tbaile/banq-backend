<?php

namespace App\Policies;

class AssetPolicy
{
    /**
     * Can the user list its own assets.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return true;
    }
}
