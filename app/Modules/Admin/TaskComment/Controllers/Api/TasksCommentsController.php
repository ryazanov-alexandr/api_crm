<?php

namespace App\Modules\Admin\TaskComment\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Task\Requests\TaskRequest;
use App\Modules\Admin\TaskComment\Models\TaskComment;
use App\Modules\Admin\TaskComment\Requests\TaskCommentRequest;
use App\Modules\Admin\TaskComment\Services\TaskCommentService;
use App\Services\Response\ResponseService;
use Illuminate\Support\Facades\Auth;

class TasksCommentsController extends Controller
{

    private  $service;

    public function __construct(TaskCommentService $service)
    {
        $this->service = $service;
    }

    public function store(TaskCommentRequest $request)
    {
        //check access
        $this->authorize('create', TaskComment::class);

        $task = $this->service->store($request, Auth::user());

        return ResponseService::sendJsonResponse(true, 200, [],[
            'item' => $task->renderdata()
        ]);

    }
}
