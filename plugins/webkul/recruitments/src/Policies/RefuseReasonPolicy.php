<?php

namespace Webkul\Recruitment\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Recruitment\Models\RefuseReason;
use Webkul\Security\Models\User;

class RefuseReasonPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_refuse::reason');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RefuseReason $refuseReason): bool
    {
        return $user->can('view_refuse::reason');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_refuse::reason');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RefuseReason $refuseReason): bool
    {
        return $user->can('update_refuse::reason');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RefuseReason $refuseReason): bool
    {
        return $user->can('delete_refuse::reason');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_refuse::reason');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, RefuseReason $refuseReason): bool
    {
        return $user->can('force_delete_refuse::reason');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_refuse::reason');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, RefuseReason $refuseReason): bool
    {
        return $user->can('restore_refuse::reason');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_refuse::reason');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, RefuseReason $refuseReason): bool
    {
        return $user->can('replicate_refuse::reason');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_refuse::reason');
    }
}
