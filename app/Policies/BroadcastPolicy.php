<?php

namespace App\Policies;

use App\Models\Broadcast;
use App\Models\User;

class BroadcastPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isDashboardUser();
    }

    public function view(User $user, Broadcast $broadcast): bool
    {
        return $user->isDashboardUser();
    }

    public function create(User $user): bool
    {
        return $user->isDashboardUser();
    }

    public function update(User $user, Broadcast $broadcast): bool
    {
        return $user->isDashboardUser();
    }

    public function delete(User $user, Broadcast $broadcast): bool
    {
        return $user->isDashboardUser();
    }
}
