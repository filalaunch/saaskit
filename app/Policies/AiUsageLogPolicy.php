<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AiUsageLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class AiUsageLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AiUsageLog');
    }

    public function view(AuthUser $authUser, AiUsageLog $aiUsageLog): bool
    {
        return $authUser->can('View:AiUsageLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AiUsageLog');
    }

    public function update(AuthUser $authUser, AiUsageLog $aiUsageLog): bool
    {
        return $authUser->can('Update:AiUsageLog');
    }

    public function delete(AuthUser $authUser, AiUsageLog $aiUsageLog): bool
    {
        return $authUser->can('Delete:AiUsageLog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AiUsageLog');
    }

    public function restore(AuthUser $authUser, AiUsageLog $aiUsageLog): bool
    {
        return $authUser->can('Restore:AiUsageLog');
    }

    public function forceDelete(AuthUser $authUser, AiUsageLog $aiUsageLog): bool
    {
        return $authUser->can('ForceDelete:AiUsageLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AiUsageLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AiUsageLog');
    }

    public function replicate(AuthUser $authUser, AiUsageLog $aiUsageLog): bool
    {
        return $authUser->can('Replicate:AiUsageLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AiUsageLog');
    }

}