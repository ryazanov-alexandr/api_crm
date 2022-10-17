<?php

namespace App\Modules\Admin\Analytics\Services;

use App\Modules\Admin\Lead\Models\Lead;
use App\Modules\Admin\Sources\Models\Source;
use Carbon\Carbon;
use DateService;
use Illuminate\Support\Facades\DB;

class AnalyticDataService
{

    public function getAnalytic($request)
    {
        $dateStart = $this->dateHelper($request->dateStart);
        $dateEnd = $this->dateHelper($request->dateEnd);

        $leadData = DB::select(
            'CALL countLeads("'.$dateStart . '","'.$dateEnd . '")'
        );

        return $leadData;
    }

    public function getSourceAnalytic($request) {
        $dateStart = $this->dateHelper($request->dateStart);
        $dateEnd = $this->dateHelper($request->dateEnd);

        $sources = (Source::all())->toArray();
        $titles = array();
        $counts = array();
        foreach (range(0,count($sources)-1) as $row) {
            $titles[$row] = $sources[$row]['title'];
            $count = $this->getCountSourceByDate($dateStart, $dateEnd, $sources[$row]['id']);
            $counts[$row] = $count;
        }
        $sourceAnalytic = ([$titles, $counts]);

        return $sourceAnalytic;
    }

    private function getCountSourceByDate($start_date, $end_date, $source) {
        $builder = DB::table('leads')->select('id')->where('source_id', '=', $source)->
                        leftJoin('sources', 'leads.source_id','=', 'sources.id', )->
                        whereDate('leads.created_at', '>=',$start_date)->
                        whereDate('leads.created_at', '<',$end_date)->count();

        return $builder;
    }

    private function dateHelper($date) {
        $newDate = Carbon::now();
        if($date && DateService::isValid($date, "d.m.Y")) {
            $newDate = Carbon::parse($date)->format('Y-m-d');
        }

        return $newDate;
    }

    public function getLeadsAnalytic($request) {
        $dateStart = $this->dateHelper($request->dateStart);
        $dateEnd = $this->dateHelper($request->dateEnd);

        $leadData = DB::select(
            'CALL leadsAnalytic("'.$dateStart . '","'.$dateEnd . '")'
        );

        return (collect($leadData)->transform(function($item) {
            return $this->renderData($item);
        }));

    }

    public function getCountQualityLeads($request) {
        $dateStart = $this->dateHelper($request->dateStart);
        $dateEnd = $this->dateHelper($request->dateEnd);

        $data = DB::select(
            'CALL getCountQualityLeads("'.$dateStart . '","'.$dateEnd . '")'
        );
        if(!$data){
            return [['Все сотрудники'], [0]];
        }

        $data = collect($data)->transform(function ($item) {
            return $this->renderDataResponsible($item);
        })->toArray();

        $responsiblesArray = array();
        $completedLeadsCountArray = array();
        foreach (range(0, count($data) - 1) as $row) {
            $responsiblesArray[$row] = $data[$row][0] . " " . $data[$row][1];
            $completedLeadsCountArray[$row] = $data[$row][2];
        }

        $responsiblesAnalytic = ([$responsiblesArray, $completedLeadsCountArray]);

        return $responsiblesAnalytic;
    }

    public function getResponsibleAnalytic($request) {
        $dateStart = $this->dateHelper($request->dateStart);
        $dateEnd = $this->dateHelper($request->dateEnd);

        $data = DB::select(
            'CALL responsiblesAnalytic("'.$dateStart . '","'.$dateEnd . '")'
        );

        $data = collect($data)->transform(function ($item) {
            return $this->renderDataResponsible($item);
        })->toArray();

        $responsiblesArray = array();
        $leadsCountArray = array();
        foreach (range(0, count($data) - 1) as $row) {
            $responsiblesArray[$row] = $data[$row][0] . " " . $data[$row][1];
            $leadsCountArray[$row] = $data[$row][2];
        }

        $responsiblesAnalytic = ([$responsiblesArray, $leadsCountArray]);

        return $responsiblesAnalytic;
    }

    private function renderData($leadsAnalytic) {
        return [
            $leadsAnalytic->CountQualityLeads,
            $leadsAnalytic->CountQualitySaleLeads,
            $leadsAnalytic->CountNotQualityLeads,
            $leadsAnalytic->CountNotQualitySaleLeads,
            $leadsAnalytic->CountExpressDelivery,
            $leadsAnalytic->CountRepeatLeads,
        ];
    }

    private function renderDataResponsible($leadsData) {
        return [
            $leadsData->firstname,
            $leadsData->lastname,
            $leadsData->CountLeads
        ];
    }
}
