<?php

namespace App\Modules\Admin\Task\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Task\Models\Task;
use App\Modules\Admin\Task\Requests\TaskRequest;
use App\Modules\Admin\Task\Services\TaskService;
use App\Modules\Admin\User\Models\User;
use App\Services\Response\ResponseService;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{

    private  $service;

    /**
     * LeadController constructor.
     * @param $service
     */
    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //check access
        $this->authorize('view', Task::class);

        $result = $this->service->getTasks();

        return ResponseService::sendJsonResponse(true, 200, [],[
            'items' => $result
        ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function archive()
    {
        //check access
        $this->authorize('view', Task::class);

        $tasks = $this->service->archive();

        return ResponseService::sendJsonResponse(true, 200, [],[
            'items' => $tasks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request)
    {
        //check access
        $this->authorize('save', Task::class);

        return ResponseService::sendJsonResponse(true, 200, [],[
            'item' => $this->service->store($request, Auth::user())
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //check access
        $this->authorize('view', Task::class);
        //
        return ResponseService::sendJsonResponse(true, 200, [],[
            'item' => $task->renderData()
        ]);

    }

    public function comments(Task $task) {

        $this->authorize('view', Task::class);

        return ResponseService::sendJsonResponse(true, 200, [],[
            'items' => $task->comments->transform(function ($item) {
                $item->load('priority', 'user', 'lead');
                $item->created_at_r = $item->created_at->toDateTimeString();
                return $item;
            })->toArray()
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function recomendedTasks(User $user) {
        $this->authorize('view', Task::class);

        $result = $this->service->getRecomendedTasks($user);

        return ResponseService::sendJsonResponse(true, 200, [],[
            'items' => $result
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function todayTasks() {
        $this->authorize('view', Task::class);

        $result = $this->service->getTodayTasks();

        return ResponseService::sendJsonResponse(true, 200, [],[
            'items' => $result
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tomorrowTasks() {
        $this->authorize('view', Task::class);

        $result = $this->service->getTomorrowTasks();

        return ResponseService::sendJsonResponse(true, 200, [],[
            'items' => $result
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function upcomingTasks() {
        $this->authorize('view', Task::class);

        $result = $this->service->getUpcomingTasks();

        return ResponseService::sendJsonResponse(true, 200, [],[
            'items' => $result
        ]);
    }

    public function expiredTasks() {
        $this->authorize('view', Task::class);

        $result = $this->service->getExpiredTasks();

        return ResponseService::sendJsonResponse(true, 200, [],[
            'items' => $result
        ]);
    }

    public function tasksByUser(User $user) {
        $this->authorize('view', Task::class);

        $result = $this->service->getTasksByUser($user);

        return ResponseService::sendJsonResponse(true, 200, [],[
            'items' => $result
        ]);
    }

    public function tasksByPriorityId($priority_id) {
        $this->authorize('view', Task::class);

        $result = $this->service->getTasksByPriority($priority_id);

        return ResponseService::sendJsonResponse(true, 200, [],[
            'items' => $result
        ]);
    }

    public function completeTasks() {
        $this->authorize('view', Task::class);

        $result = $this->service->getCompleteTasks();

        return ResponseService::sendJsonResponse(true, 200, [],[
            'items' => $result
        ]);
    }

    public function countUserTasks() {
        $this->authorize('view', Task::class);

        $count = $this->service->getCountUserTasks();

        return ResponseService::sendJsonResponse(true, 200, [],[
            'item' => $count
        ]);
    }

    public function countUserTasksExpiring() {
        $this->authorize('view', Task::class);

        $count = $this->service->getCountUserTasksExpiring();

        return ResponseService::sendJsonResponse(true, 200, [],[
            'item' => $count
        ]);
    }
}
