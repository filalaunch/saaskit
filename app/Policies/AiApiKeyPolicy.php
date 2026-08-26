<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AiApiKey;
use Illuminate\Auth\Access\HandlesAuthorization;

class AiApiKeyPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AiApiKey');
    }

    public function view(AuthUser $authUser, AiApiKey $aiApiKey): bool
    {
        return $authUser->can('View:AiApiKey');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AiApiKey');
    }

    public function update(AuthUser $authUser, AiApiKey $aiApiKey): bool
    {
        return $authUser->can('Update:AiApiKey');
    }

    public function delete(AuthUser $authUser, AiApiKey $aiApiKey): bool
    {
        return $authUser->can('Delete:AiApiKey');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AiApiKey');
    }

    public function restore(AuthUser $authUser, AiApiKey $aiApiKey): bool
    {
        return $authUser->can('Restore:AiApiKey');
    }

    public function forceDelete(AuthUser $authUser, AiApiKey $aiApiKey): bool
    {
        return $authUser->can('ForceDelete:AiApiKey');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AiApiKey');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AiApiKey');
    }

    public function replicate(AuthUser $authUser, AiApiKey $aiApiKey): bool
    {
        return $authUser->can('Replicate:AiApiKey');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AiApiKey');
    }

}