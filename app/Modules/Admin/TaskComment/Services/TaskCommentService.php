<?php

namespace App\Modules\Admin\TaskComment\Services;

use App\Modules\Admin\Lead\Models\Lead;
use App\Modules\Admin\Priority\Models\Priority;
use App\Modules\Admin\Task\Models\Task;
use App\Modules\Admin\TaskComment\Models\TaskComment;
use App\Modules\Admin\User\Models\User;

class TaskCommentService
{
    public static function saveComment(string $text, Task $task, User $user, Priority $priority, Lead $lead, string $commentValue = null, $is_event = false) {

        $comment = new TaskComment([
            'text' => $text,
            'comment_value' => $commentValue,
        ]);

        $comment->is_event = $is_event;

        $comment
            ->task()
            ->associate($task)
            ->user()
            ->associate($user)
            ->lead()
            ->associate($lead)
            ->priority()
            ->associate($priority)
            ->save();

        return $comment;
    }

    public function store($request, $user)
    {
        $task = Task::findOrFail($request->task_id);
        if($task) {

            $priority = Priority::findOrFail($request->priority_id);
            $responsible_user = User::findOrFail($request->responsible_id);
            $lead = Lead::findOrFail($request->lead_id);

            if($request->responsible_id != $task->responsible_id) {
                //$task->responsible_id = $request->responsible_id;
                $task->responsibleUser()->associate($responsible_user);
                $tmpText = "Пользователь <strong>" . $user->fullname . '</strong> изменил <strong>ответственного</strong> на '. $responsible_user->fullname;
                TaskCommentService::saveComment($tmpText, $task, $user, $priority, $lead,null, true);
            }

            if($request->priority_id != $task->priority_id) {
                $task->priority()->associate($priority);

                $tmpText = "Пользователь <strong>" . $user->fullname . '</strong> изменил <strong>приоритет</strong> на '. $priority->title_ru;
                TaskCommentService::saveComment($tmpText, $task, $user, $priority, $lead,null, true);

            }

            if($request->is_complete != $task->is_complete) {
                $task->is_complete = 1;
                $tmpText = "Пользователь <strong>" .$responsible_user->fullname . '</strong> выполнил задание '. $task->title;
                TaskCommentService::saveComment($tmpText, $task, $user, $priority, $lead,null, true);
            }

            $task->save();

            if(isset($request->text) && $request->text != "") {
                $tmpText = "Пользователь <strong>" . $user->fullname . '</strong> оставил <strong>комментарий</strong> '. $request->text;
                TaskCommentService::saveComment($tmpText, $task, $user, $priority, $lead, $request->text);
            }

        }

        return $task;
    }
}
