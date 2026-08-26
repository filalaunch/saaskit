<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AiModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class AiModelPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AiModel');
    }

    public function view(AuthUser $authUser, AiModel $aiModel): bool
    {
        return $authUser->can('View:AiModel');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AiModel');
    }

    public function update(AuthUser $authUser, AiModel $aiModel): bool
    {
        return $authUser->can('Update:AiModel');
    }

    public function delete(AuthUser $authUser, AiModel $aiModel): bool
    {
        return $authUser->can('Delete:AiModel');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AiModel');
    }

    public function restore(AuthUser $authUser, AiModel $aiModel): bool
    {
        return $authUser->can('Restore:AiModel');
    }

    public function forceDelete(AuthUser $authUser, AiModel $aiModel): bool
    {
        return $authUser->can('ForceDelete:AiModel');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AiModel');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AiModel');
    }

    public function replicate(AuthUser $authUser, AiModel $aiModel): bool
    {
        return $authUser->can('Replicate:AiModel');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AiModel');
    }

}