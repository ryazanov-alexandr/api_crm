<?php

namespace App\Modules\Admin\Menu\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Menu\Models\Menu;
use App\Services\Response\ResponseService;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return ResponseService::sendJsonResponse(true, 200,[], [
            'items' => ((Menu::frontMenu(Auth::user())->get())->toArray())
        ]);
    }

}
