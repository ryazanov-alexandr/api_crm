<?php

namespace App\Modules\Admin\TaskComment\Policy;
use App\Modules\Admin\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskCommentPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function view(User $user)
    {
        return $user->canDo(['SUPER_ADMINISTRATOR','TASKS_COMMENT_VIEW']);
    }

    public function create(User $user)
    {
        return $user->canDo(['SUPER_ADMINISTRATOR','TASKS_COMMENT_CREATE']);
    }

    public function edit(User $user)
    {
        return $user->canDo(['SUPER_ADMINISTRATOR','TASKS_COMMENT_EDIT']);
    }

    public function delete(User $user)
    {
        return $user->canDo(['SUPER_ADMINISTRATOR','TASKS_COMMENT_EDIT']);
    }
}
