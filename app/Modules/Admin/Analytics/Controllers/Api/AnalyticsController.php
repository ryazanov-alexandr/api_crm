<?php

namespace App\Modules\Admin\Analytics\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Analytics\Export\LeadsExport;
use App\Modules\Admin\Analytics\Services\AnalyticDataService;
use App\Modules\Admin\Lead\Models\Lead;
use App\Modules\Admin\User\Models\User;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Excel;

class AnalyticsController extends Controller
{

    private $service;

    public function __construct(AnalyticDataService $service) {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAnalytic', Lead::class);

        $leadsData = $this->service->getAnalytic($request);

        return ResponseService::sendJsonResponse(true, 200, [], [
            'items' => $leadsData
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sourcesAnalytic(Request $request)
    {
        $this->authorize('viewAnalytic', Lead::class);

        $leadsSourceData = $this->service->getSourceAnalytic($request);

        return ResponseService::sendJsonResponse(true, 200, [], [
            'item' => $leadsSourceData
        ]);
    }

    public function leadsAnalytic(Request $request)
    {
        $this->authorize('viewAnalytic', Lead::class);

        $leadsSourceData = $this->service->getLeadsAnalytic($request);

        return ResponseService::sendJsonResponse(true, 200, [], [
            'item' => $leadsSourceData
        ]);
    }

    public function responsiblesAnalytic(Request $request) {
        $this->authorize('viewAnalytic', Lead::class);

        $leadsSourceData = $this->service->getResponsibleAnalytic($request);

        return ResponseService::sendJsonResponse(true, 200, [], [
            'item' => $leadsSourceData
        ]);
    }

    public function countQualityLeads(Request $request) {
        $this->authorize('viewAnalytic', Lead::class);

        $leadsSourceData = $this->service->getCountQualityLeads($request);

        return ResponseService::sendJsonResponse(true, 200, [], [
            'item' => $leadsSourceData
        ]);
    }

}
