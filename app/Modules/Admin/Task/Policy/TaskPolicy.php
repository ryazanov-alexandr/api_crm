<?php

namespace App\Modules\Admin\Task\Policy;

use App\Modules\Admin\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function view(User $user){
        return $user->canDo(['SUPER_ADMINISTRATOR','TASKS_VIEW', 'TASKS_ACCESS']);
    }


    public function save(User $user){

        return $user->canDo(['SUPER_ADMINISTRATOR','TASKS_CREATE', 'TASKS_ACCESS']);
    }


    public function edit(User $user){
        return $user->canDo(['SUPER_ADMINISTRATOR','TASKS_EDIT', 'TASKS_ACCESS']);
    }


    public function delete(User $user){
        return $user->canDo(['SUPER_ADMINISTRATOR','TASKS_EDIT', 'TASKS_ACCESS']);
    }
}
