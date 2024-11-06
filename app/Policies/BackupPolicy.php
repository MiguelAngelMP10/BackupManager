<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Backup;
use Illuminate\Auth\Access\HandlesAuthorization;

class BackupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_backup');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Backup $backup): bool
    {
        return $user->can('view_backup');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_backup');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Backup $backup): bool
    {
        return $user->can('update_backup');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Backup $backup): bool
    {
        return $user->can('delete_backup');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_backup');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Backup $backup): bool
    {
        return $user->can('force_delete_backup');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_backup');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Backup $backup): bool
    {
        return $user->can('restore_backup');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_backup');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Backup $backup): bool
    {
        return $user->can('replicate_backup');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_backup');
    }
}
