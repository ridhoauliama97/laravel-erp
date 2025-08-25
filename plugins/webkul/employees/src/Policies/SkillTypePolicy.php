<?php

namespace Webkul\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Employee\Models\SkillType;
use Webkul\Security\Models\User;

class SkillTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_skill::type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SkillType $skillType): bool
    {
        return $user->can('view_skill::type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_skill::type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SkillType $skillType): bool
    {
        return $user->can('update_skill::type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SkillType $skillType): bool
    {
        return $user->can('delete_skill::type');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_skill::type');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, SkillType $skillType): bool
    {
        return $user->can('force_delete_skill::type');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_skill::type');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, SkillType $skillType): bool
    {
        return $user->can('restore_skill::type');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_skill::type');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, SkillType $skillType): bool
    {
        return $user->can('replicate_skill::type');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_skill::type');
    }
}
