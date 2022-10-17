<?php

namespace App\Modules\Admin\Priority\Controllers\Api;

use App\Modules\Admin\Priority\Models\Priority;
use App\Modules\Admin\Priority\Services\PriorityService;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PriorityController extends Controller
{

    private $service;

    public function __construct(PriorityService $priorityService) {
        $this->service = $priorityService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new Priority());

        return ResponseService::sendJsonResponse(true, 200, [], [
            'items' => $this->service->getPriority()
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Modules\Admin\Priority\Models\Priority  $priority
     * @return \Illuminate\Http\Response
     */
    public function show(Priority $priority)
    {
        return ResponseService::sendJsonResponse(true, 200, [], [
            'item' => $priority->toArray()
        ]);
    }

}
