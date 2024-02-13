<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    /**
     * Can the user view any assets
     */
    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, Asset $asset): bool
    {
        return $user->id == $asset->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return true;
    }
}
