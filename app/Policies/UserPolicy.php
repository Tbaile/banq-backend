<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Allow user to view the resource only if it's himself.
     */
    public function view(User $user, User $model): bool
    {
        return $user->is($model);
    }

    /**
     * User can update the resource only if it's himself.
     */
    public function update(User $user, User $model): bool
    {
        return $user->is($model);
    }
}
