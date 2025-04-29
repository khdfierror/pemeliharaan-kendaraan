<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Merk;
use Illuminate\Auth\Access\HandlesAuthorization;

class MerkPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_merk');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Merk $merk): bool
    {
        return $user->can('view_merk');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_merk');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Merk $merk): bool
    {
        return $user->can('update_merk');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Merk $merk): bool
    {
        return $user->can('delete_merk');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_merk');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Merk $merk): bool
    {
        return $user->can('force_delete_merk');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_merk');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Merk $merk): bool
    {
        return $user->can('restore_merk');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_merk');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Merk $merk): bool
    {
        return $user->can('replicate_merk');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_merk');
    }
}
