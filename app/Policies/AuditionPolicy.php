<?php

namespace App\Policies;

use App\Models\Audition;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AuditionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Audition $audition): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create audition');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Audition $audition): bool
    {
       return $user->hasPermissionTo(permission: 'edit audition');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Audition $audition): bool
    {
        return $user->hasPermissionTo('delete audition');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Audition $audition): bool
    {
        return $user->hasPermissionTo('delete audition');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Audition $audition): bool
    {
        return $user->hasPermissionTo('delete audition');
    }
}
