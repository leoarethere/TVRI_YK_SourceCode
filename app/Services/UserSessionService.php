<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UserSessionService
{
    /**
     * Revoke every database-backed session owned by the given user.
     */
    public function revokeAll(User $user): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))
            ->table(config('session.table', 'sessions'))
            ->where('user_id', $user->getAuthIdentifier())
            ->delete();
    }
}
