<?php

namespace App\Policies;

use App\Models\QualityTest;
use App\Models\User;

class QualityTestPolicy
{
    public function update(User $user, QualityTest $qualityTest): bool
    {
        return $user->hasRole('superadmin') || $user->id === $qualityTest->tested_by;
    }

    public function delete(User $user, QualityTest $qualityTest): bool
    {
        return $user->hasRole('superadmin') || $user->id === $qualityTest->tested_by;
    }
}
