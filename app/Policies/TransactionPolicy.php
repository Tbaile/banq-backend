<?php

namespace App\Policies;

use App\Models\User;

class TransactionPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }
}