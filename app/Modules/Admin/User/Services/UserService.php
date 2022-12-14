<?php

namespace App\Modules\Admin\User\Services;

use App\Modules\Admin\Role\Models\Role;
use App\Modules\Admin\User\Models\User;
use App\Modules\Admin\User\Requests\UserRequest;
use App\Modules\Admin\User\Requests\UserRequestWeb;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function getUsers($status = false)
    {

        $usersBuilder = User::with('roles' );

        $users = $usersBuilder->get();
        if($status) {
            $usersBuilder->where('status', 1);
        }

        $users->transform(function ($item) {
            $item->rolename = '';
            if(isset($item->roles)) {
                $item->rolename = isset($item->roles->first()->title) ? $item->roles->first()->title : "";
            }

            return $item;
        });

        return $users;
    }

    public function getPermissions($user) {

        /*$perms = collect($user->getMergedPermissions())->map(function($item) {
            return $item['alias'];
        })->toArray();*/
        $perms = collect($user->getMergedPermissions());

        return $perms;
    }

    public function save(UserRequest $request, User $user)
    {
        $user->fill($request->only($user->getFillable()));

        $user->password = Hash::make($request->password);
        $user->status = '1';

        $user->save();

        $role = Role::findOrFail($request->role_id);
        $user->roles()->sync($role->id);

        $user->rolename = $role->title;

        return $user;
    }

    public function saveWeb(UserRequestWeb $request, User $user)
    {
        //одинаковый код, так как мы разграничиваем api и web запросы
        $user->fill($request->only($user->getFillable()));

        if($request->password){
            $user->password = Hash::make($request->password);
        }

        $user->status = '1';

        $user->save();

        $role = Role::findOrFail($request->role_id);
        $user->roles()->sync($role->id);

        $user->rolename = $role->title;

        return $user;
    }

}
