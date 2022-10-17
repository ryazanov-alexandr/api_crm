<?php

namespace App\Modules\Admin\Task\Services;

use App\Modules\Admin\Lead\Models\Lead;
use App\Modules\Admin\Priority\Models\Priority;
use App\Modules\Admin\Task\Models\Task;
use App\Modules\Admin\TaskComment\Services\TaskCommentService;
use App\Modules\Admin\User\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    public function getTasks()
    {
        $tasks = (new Task())->getTasks(Auth::user());


        $resultTasks = $this->renderCollection($tasks);

        return $resultTasks;

    }

    private function backpackAlgorithm($tasks, $minuteToEnd) {
        $F = array();
        $countTasks = count($tasks);

        foreach (range(0,$countTasks) as $row) {
            foreach (range(0,$minuteToEnd) as $col) {
                $F[$row][$col] = 0;
            }
        }

        for($i = 0; $i <= $countTasks; $i++) {
            for ($j = 0; $j <= $minuteToEnd; $j++) {
                if($i == 0 || $j == 0) {
                    $F[0][$i] = 0;
                } else {
                    if((int)$tasks[$i-1]['time_to_complete']/15 > $j) {
                        $F[$i][$j] = $F[$i - 1][$j];
                    } else {
                        $F[$i][$j] = max($F[$i - 1][$j - $tasks[$i-1]['time_to_complete']/15] + $tasks[$i-1]['priority_id'], $F[$i - 1][$j]);
                    }

                }
            }
        }

        return $F;
    }

    public function getRecomendedTasks($user) {
        $tasks = (new Task())->getTasksByUser($user);
        $recTasks = [];
        if($tasks) {

            $minuteToEnd = intdiv($this->getMinutes(), 15);
            $F = $this->backpackAlgorithm($tasks, $minuteToEnd);

            $tasksId = $this->getRecTasksId($minuteToEnd, $F, $tasks);

            $recTasks = collect($tasks)->whereIn('id', $tasksId);
        }
        $recTasks = $this->renderCollection($recTasks);

        return $recTasks;
    }

    private function getMinutes($nextDateMinutes = 0)
    {
        //nextDateMinutes добавляет минуты если задания на завтра или на неделю
        date_default_timezone_set('Europe/Moscow');
        $start_date =  Carbon::now();
        $end_date = (Carbon::now()->startOfDay())->addHours(18);
        $time = $start_date->diffInMinutes($end_date, false) + $nextDateMinutes;
        if($time < 0) {
            return 0;
        }
        return $time;
    }

    private function renderCollection($tasks) {
        return (collect($tasks)->transform(function($item) {
            return $item->renderData(false);
        }));
    }

    public function store($request, User $user)
    {
        $task = new Task();
        $task->fill($request->only($task->getFillable()));

        $priority = Priority::findOrFail($request->priority_id);

        //lead save
        $task->priority()->associate($priority); //привязали статус к task

        $lead = Lead::findOrFail($request->lead_id);
        $task->lead()->associate($lead);

        $user->authorTasks()->save($task); //сохранили task в базу данных к текущему юзеру

        $this->addTasksComments($task, $user, $priority, $lead, $request);

        return $task->renderData();;
    }

    public function getCountUserTasks() {
        return (new Task())->getCountUserTasks(Auth::user());
    }

    public function getCountUserTasksExpiring() {
        $count =  (new Task())->getCountUserTasksExpiring(Auth::user());
        return [
            $count[0]->CountTasks
        ];

    }

    public function archive()
    {
        $tasks = (new Task())->getArchives(Auth::user());
        return (collect($tasks->items())->transform(function($item) {
            return $item->renderData(false);
        }));
    }

    private function addTasksComments($task, $user, $priority, $lead, $request)
    {
        $is_event = true;
        $tmpText = "Автор ".$user->fullname.' создал задачу с приоритетом '.$priority->title_ru;
        TaskCommentService::saveComment($tmpText, $task, $user, $priority, $lead, null, $is_event);

        if (isset($request->text) && $request->text != "") {
            $tmpText = "Пользователь <strong>" . $user->fullname . '</strong> оставил <strong>комментарий</strong> ' . $request->text;
            TaskCommentService::saveComment($tmpText, $task, $user, $priority, $lead, $request->text);
        }
    }


    public function getTasksByPriority($priority_id) {
        $tasks = (new Task())->getTasksByPriority(Auth::user(), $priority_id);

        return $tasks->transform(function($item) {
            return $item->renderData(false);
        });
    }



    public function getTodayTasks() {
        $tasks = (new Task())->getTodayTasks(Auth::user());
        $recTasks = [];
        if($tasks) {

            $minuteToEnd = intdiv($this->getMinutes(), 15);

            $F = $this->backpackAlgorithm($tasks, $minuteToEnd);

            $tasksId = $this->getRecTasksId($minuteToEnd, $F, $tasks);

            $recTasks = collect($tasks)->whereIn('id', $tasksId);


        }

        $recTasks = $this->renderCollection($recTasks);

        return $recTasks;
    }

    public function getTomorrowTasks() {
        $tasks = (new Task())->getTomorrowTasks(Auth::user());
        $recTasks = [];
        if($tasks) {

            $minuteToEnd = intdiv($this->getMinutes(480), 15);

            $F = $this->backpackAlgorithm($tasks, $minuteToEnd);

            $tasksId = $this->getRecTasksId($minuteToEnd, $F, $tasks);

            $recTasks = collect($tasks)->whereIn('id', $tasksId);

            $recTasks = $this->renderCollection($recTasks);

        }

        return $recTasks;
    }

    public function getExpiredTasks() {
        $tasks = (new Task())->getExpiredTasks(Auth::user());

        $tasks = $this->renderCollection($tasks);

        return $tasks;
    }

    public function getCompleteTasks() {
        $tasks = (new Task())->getCompleteTasks(Auth::user());

        $tasks = $this->renderCollection($tasks);

        return $tasks;
    }

    public function getTasksByUser($user) {
        $tasks = (new Task())->getTasksByUser($user);

        $tasks = $this->renderCollection($tasks);

        return $tasks;
    }

    private function getRecTasksId($minuteToEnd, $F, $tasks) {
        $isAdd = [];
        $countTasks = count($tasks);
        for ($j = $countTasks; $j >= 1; $j--) {
            if ($F[$j][$minuteToEnd] != $F[$j-1][$minuteToEnd]) {
                array_push($isAdd, $tasks[$j-1]['id']);
                $minuteToEnd -= $tasks[$j-1]['time_to_complete']/15;
            }
        }

        return $isAdd;
    }

    public function getUpcomingTasks()
    {
        $tasks = (new Task())->getUpcomingTasks(Auth::user());
        $recTasks = [];
        if($tasks) {

            $minuteToEnd = intdiv($this->getMinutes(480 * 7), 15);

            $F = $this->backpackAlgorithm($tasks, $minuteToEnd);

            $tasksId = $this->getRecTasksId($minuteToEnd, $F, $tasks);

            $recTasks = collect($tasks)->whereIn('id', $tasksId);

            $recTasks = $this->renderCollection($recTasks);

        }

        return $recTasks;
    }
}
