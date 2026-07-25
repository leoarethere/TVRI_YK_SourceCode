<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isDashboardUser();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isDashboardUser();
    }

    public function create(User $user): bool
    {
        return $user->isDashboardUser();
    }

    public function update(User $user, User $model): bool
    {
        if (! $user->isDashboardUser()) {
            return false;
        }

        return true;
    }

    public function delete(User $user, User $model): bool
    {
        if (! $user->isDashboardUser()) {
            return false;
        }

        return true;
    }
}
