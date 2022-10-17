<?php

namespace App\Modules\Admin\Lead\Services;

use App\Modules\Admin\Lead\Models\Lead;
use App\Modules\Admin\Lead\Requests\LeadCreateRequest;
use App\Modules\Admin\LeadComment\Services\LeadCommentService;
use App\Modules\Admin\Status\Models\Status;
use App\Modules\Admin\User\Models\User;
use Illuminate\Support\Facades\Auth;

class LeadService
{

    public function getLeads()
    {
        $leads = (new Lead())->getLeads(Auth::user());
        $statuses = Status::all();

        $resultLeads = [];

        $statuses->each(function ($item, $key) use($leads, &$resultLeads) {
            $collection = $leads->where('status_id', $item->id);
            $resultLeads[$item->title] = array_values($collection->map(function ($elem) {
                return $elem->renderData();
            })->toArray());
        });

        return $resultLeads;
    }

    public function getNotDoneLeads()
    {
        $leads = (new Lead())->getNotDoneLeads(Auth::user());

        return (collect($leads)->transform(function ($item) {
            return $item->renderData(false);
        }));

    }

    public function getLeadsDoneToday() {
        $leads = (new Lead())->getLeadsDoneToday(Auth::user());

        return (collect($leads)->transform(function ($item) {
            return $item->renderData(false);
        }));
    }

    public function store(LeadCreateRequest $request, User $user)
    {
        $lead = new Lead();

        $lead->fill($request->only($lead->getFillable()));

        $status = Status::where('title', 'new')->first();

        $lead->status()->associate($status); //привязали статус к лиду

        $user->leads()->save($lead); //сохранили лид в базу данных к текущему юзеру

        //add comment
        $this->addStoreComments($lead, $request, $user, $status);

        $lead->statuses()->attach($status->id);

        return $lead;
    }

    private function addStoreComments($lead, $request, $user, $status)
    {
        $is_event = true;
        $tempText = "Автор <strong>".$user->getFullnameAttribute().'</strong> создал лид со статусом '.$status->title_ru;
        LeadCommentService::saveComment($tempText, $lead, $user, $status, null, $is_event);

        if($request->text) {
            $is_event = false;
            $tempText = "Пользователь <strong>".$user->getFullnameAttribute().'</strong> оставил комментарий '.$request->text;
            LeadCommentService::saveComment($tempText, $lead, $user, $status, $request->text, $is_event);
        }

    }

    public function update(LeadCreateRequest $request, $user, $lead)
    {
        $tmpLead = clone $lead;

        $lead->count_create++;

        $status = Status::where('title', 'new')->first();
        $lead->fill($request->only($lead->getFillable()));
        $lead->status()->associate($status); //привязали статус к лиду
        $lead->save();

        //add comment
        $this->addUpdateComments($lead, $request, $user, $status, $tmpLead);

        return $lead;
    }

    private function addUpdateComments($lead, LeadCreateRequest $request, $user, $status, $tmp)
    {
        //если есть коммент
        if ($request->text) {
            $tmpText = "Пользователь " . $user->fullname . ' оставил комментарий ' .  $request->text ;
            LeadCommentService::saveComment($tmpText, $lead, $user, $status, $request->text);
        }

        //если изменен источник лида
        if ($tmp->source_id != $lead->source_id) {
            $is_event = true;
            $tmpText = "Пользователь " . $user->fullname . ' изменил источник на ' . $lead->source->title;
            LeadCommentService::saveComment($tmpText, $lead, $user, $status,null,$is_event);
        }

        //если изменен подразделение лида
        if ($tmp->unit_id != $lead->unit_id) {
            $is_event = true;
            $tmpText = "Пользователь " . $user->fullname . ' изменил подразделение на ' . $lead->unit->title;
            LeadCommentService::saveComment($tmpText, $lead, $user, $status,null,$is_event);
        }

        //если измене статус
        if ($tmp->status_id != $lead->status_id) {
            $is_event = true;
            $tmpText = "Пользователь " . $user->fullname . ' изменил статус на ' . $lead->status->title_ru;
            LeadCommentService::saveComment($tmpText, $lead, $user, $status,null,$is_event);
        }

        $is_event = true;
        /**Автор лида* создал лид *дата и время создания* со статусом *статус**/
        $tmpText = "Автор " . $user->fullname . ' создал лид  со статусом ' . $status->title_ru;
        LeadCommentService::saveComment($tmpText, $lead, $user, $status, $request->text, $is_event);

        $lead->statuses()->attach($status->id);

    }

    public function archive()
    {
        $leads = (new Lead())->getArchive();

        return (collect($leads->items())->transform(function ($item) {
            return $item->renderData(false);
        }));
    }

    public function checkExist($request)
    {
        $queryBuilder = Lead::select('*');//выбрать все

        if($request->link) {
            $queryBuilder->where('link', $request->link);
        } elseif($request->phone) {
            $queryBuilder->where('phone', $request->phone);
        }

        $queryBuilder->where('status_id', '!=', Lead::DONE_STATUS);

        return $queryBuilder->first();
    }

    public function updateQuality($request, $lead)
    {
        $lead->isQualityLead = true;
        $lead->save();

        return $lead;
    }

    public function getAddSaleCount() : int
    {
        $user = Auth::user();

        $count = $user->
            leads()->
            where('is_add_sale', '1')->
            where('isQualityLead', '1')->
            where(\DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), '>', \DB::raw('DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)'))->
            count();

        return $count;
    }
}
