<?php

namespace App\Modules\Admin\LeadComment\Services;

use App\Modules\Admin\Lead\Models\Lead;
use App\Modules\Admin\LeadComment\Models\LeadComment;
use App\Modules\Admin\Status\Models\Status;
use App\Modules\Admin\User\Models\User;

class LeadCommentService
{
    public static function saveComment(string $text, Lead $lead, User $user, Status $status, string $commentValue = null, bool $is_event = false) {

        $comment = new LeadComment([
            'text' => $text,
            'comment_value' => $commentValue,
        ]);

        $comment->is_event = $is_event;

        $comment->
            lead()->associate($lead)->
            user()->associate($user)->
            status()->associate($status)->
            save();

        return $comment;
    }

    public function store($request, User $user)
    {
        $lead = Lead::findOrFail($request->lead_id);

        if ($lead) {
            $status = Status::findOrFail($request->status_id);
            $responsible_user = User::findOrFail($request->responsible_id);

            if($request->responsible_id != $lead->responsible_id) {
                $lead->responsibleUser()->associate($responsible_user);
                $tmpText = "Пользователь <strong>" . $user->fullname . '</strong> изменил <strong>ответственного</strong> на '. $responsible_user->fullname;
                LeadCommentService::saveComment($tmpText, $lead, $user, $status, null, true);
            }
            //если текущий статус не равен статусу, который приходит из запроса, то юзер меняет статус
            if ($status->id != $lead->status_id) {
                $lead->status()->associate($status)->update();

                $is_event = true;
                $tempText = "Пользователь <strong>" . $user->getFullnameAttribute() . '</strong> изменил статус лида ' . $status->title_ru;
                LeadCommentService::saveComment($tempText, $lead, $user, $status, null, $is_event);

                $lead->statuses()->attach($status->id);//связь между лидом и новым статусом (lead_status table)
            }

            if ($request->user_id && $request->user_id != $lead->user_id) {
                $newUser = User::findOrFail($request->user_id);
                $lead->user()->associate($newUser)->update();

                $is_event = true;
                $tempText = "Пользователь <strong>" .
                    $user->getFullnameAttribute() . '</strong> изменил автора лида на ' . $newUser->getFullnameAttribute();
                LeadCommentService::saveComment($tempText, $lead, $user, $status, null, $is_event);
            }

            if ($request->text) {
                $tempText = "Пользователь <strong>" . $user->getFullnameAttribute() . '</strong> оставил комментарий ' . $request->text;
                LeadCommentService::saveComment($tempText, $lead, $user, $status, $request->text);
            }

            $lead->save();
        }

        return $lead;
    }
}
