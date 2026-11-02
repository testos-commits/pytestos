<?php

namespace App\Policies;

use App\Models\RewardRefferal;
use App\Models\User;
use App\Models\RewardReferral;
use Illuminate\Auth\Access\Response;

class RewardReferralPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super admin', 'country admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RewardReferral $rewardReferral): bool
    {
        return $user->hasRole(['super admin', 'country admin']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super admin', 'country admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RewardReferral $rewardReferral): bool
    {
        return $user->hasRole(['super admin', 'country admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RewardReferral $rewardReferral): bool
    {
        return $user->hasRole(['super admin', 'country admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RewardReferral $rewardReferral): bool
    {
        return $user->hasRole(['super admin', 'country admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RewardReferral $rewardReferral): bool
    {
        return $user->hasRole(['super admin', 'country admin']);
    }
}
