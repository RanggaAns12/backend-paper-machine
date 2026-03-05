<?php

namespace App\Policies;

use App\Models\WinderLog;
use App\Models\User;

class WinderLogPolicy
{
    public function update(User $user, WinderLog $winderLog): bool
    {
        return $user->hasRole('superadmin') || $user->id === $winderLog->operator_id;
    }

    public function delete(User $user, WinderLog $winderLog): bool
    {
        return $user->hasRole('superadmin') || $user->id === $winderLog->operator_id;
    }
}
