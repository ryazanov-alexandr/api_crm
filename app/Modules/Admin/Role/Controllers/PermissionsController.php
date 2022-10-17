<?php

namespace App\Modules\Admin\Role\Controllers;

use App\Modules\Admin\Dashboard\Classes\Base;
use App\Modules\Admin\Role\Models\Permission;
use App\Modules\Admin\Role\Models\Role;
use App\Modules\Admin\Role\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionsController extends Base
{

    public function __construct(PermissionService $permissionService)
    {
        parent::__construct();
        $this->service = $permissionService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', Role::class);

        $perms = Permission::all();
        $roles = Role::all();

        $this->title = "Title Permission Index";

        $this->content = view('Admin::Permission.index')->
        with([
            'perms' => $perms,
            'roles' => $roles,
            'title' => $this->title,
        ])->
        render();

        return $this->renderOutput();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $this->service->save($request);

        return back()->with([
            'message' => __('Success')
        ]);
    }
}
