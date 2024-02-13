<?php

namespace App\Policies;

class TransactionPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return true;
    }
}
