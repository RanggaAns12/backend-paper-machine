<?php

namespace App\Policies;

use App\Models\PaperMachineReport;
use App\Models\User;

class PaperMachineReportPolicy
{
    public function update(User $user, PaperMachineReport $report): bool
    {
        return $user->hasRole('superadmin') || $user->id === $report->operator_id;
    }

    public function delete(User $user, PaperMachineReport $report): bool
    {
        return $user->hasRole('superadmin') || $user->id === $report->operator_id;
    }
}
