<?php

namespace App\Policies;

use App\Models\AdminCountry;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdminCountryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdminCountry $adminCountry): bool
    {
        return $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AdminCountry $adminCountry): bool
    {
        return $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdminCountry $adminCountry): bool
    {
        return $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AdminCountry $adminCountry): bool
    {
        return $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AdminCountry $adminCountry): bool
    {
        return $user->hasRole('super admin');
    }
}
