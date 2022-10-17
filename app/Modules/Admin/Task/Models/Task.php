<?php

namespace App\Modules\Admin\Task\Models;

use App\Modules\Admin\Lead\Models\Lead;
use App\Modules\Admin\Priority\Models\Priority;
use App\Modules\Admin\TaskComment\Models\TaskComment;
use App\Modules\Admin\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'author_id',
        'responsible_id',
        'priority_id',
        'lead_id',
        'time_to_complete',
        'due_date',
        'is_complete'
    ];

    public function getTasks(User $user) {
        $start_date = Carbon::now();
        $end_date = new Carbon('first day of January 2099');

        return $this->getTasksByDate($user, $start_date, $end_date);

    }

    private function getTasksByDate(User $user, $start_date, $end_date) {
        $builder = $this->
                    with([
                        'priority',
                        'lead',
                        'responsibleUser'
                    ])->
                    where(function($query) use($start_date, $end_date) {
                        $query->where('is_complete', 0)->
                                where([
                                    ['due_date', '>=',$start_date],
                                    ['due_date', '<',$end_date]
                                ]);
                    });

        if(!$user->hasRole('SUPER_ADMINISTRATOR')) {
            $builder->where(function ($query) use ($user) {
                $query->
                where('author_id', $user->id)->
                orWhere('responsible_id', $user->id);
            });
        }//если юзер менеджер, то он будет видеть свои задачи, если админ, то все задачи

        return $builder->orderBy('created_at')->get();
    }

    public function getTasksByPriority(User $user, $priority_id) {
        $builder = $this->
                    with([
                        'priority',
                        'lead',
                        'responsibleUser'
                    ])->
                    where(function($query) use($priority_id) {
                        $query->where([
                            ['priority_id', '=', $priority_id],
                        ]);
                    });

        if(!$user->hasRole('SUPER_ADMINISTRATOR')) {
            $builder->where(function ($query) use ($user) {
                $query->
                where('author_id', $user->id)->
                orWhere('responsible_id', $user->id);
            });
        }//если юзер менеджер, то он будет видеть свои задачи, если админ, то все задачи

        return $builder->orderBy('created_at')->get();
    }

    public function getTodayTasks(User $user) {
        $start_date = Carbon::now();
        $end_date = (Carbon::now()->startOfDay())->addDay(1);

        return $this->getTasksByDate($user, $start_date, $end_date);

    }

    public function getTomorrowTasks(User $user) {
        $start_date = (Carbon::now()->nextWeekday())->startOfDay();
        $end_date = (Carbon::now()->nextWeekday())->startOfDay()->addDay(1);

        return $this->getTasksByDate($user, $start_date, $end_date);

    }

    public function getUpcomingTasks(User $user) {
        $start_date = Carbon::now()->nextWeekday()->startOfDay()->addDay(1);
        $end_date = Carbon::now()->addDay(6)->endOfDay();

        return $this->getTasksByDate($user, $start_date, $end_date);

    }

    public function getExpiredTasks(User $user) {
        $start_date = new Carbon('first day of January 2022');
        $end_date = Carbon::now();

        return $this->getTasksByDate($user, $start_date, $end_date);

    }
    public function getTasksByUser($user) {
        $user = User::findOrFail($user->id);
        if($user) {
            $builder = $this->
            with([
                'priority',
                'lead',
                'responsibleUser'
            ])->
            where(function ($query) use ($user) {
                $query->
                where('due_date', '>', Carbon::now())->
                where('is_complete', 0)->
                where('responsible_id', $user->id);
            });

            return $builder->
            orderBy('due_date')->
            get();
        }
    }

    public function getCountUserTasks(User $user) {
        $user = User::findOrFail($user->id);
        if($user) {
            $builder = \DB::table('tasks')->select('id')->where([
                    ['due_date', '>', Carbon::now()],
                    ['is_complete', 0],
                    ['responsible_id', $user->id]
                    ])->count();

            return $builder;
        }
    }
    //истекающие таски меньше часа до срока выполнения
    public function getCountUserTasksExpiring(User $user) {
        $user = User::findOrFail($user->id);
        if($user) {
            $builder = DB::select(
                'CALL countUserTasksExpiring("'.$user->id.'")'
            );

            return $builder;
        }
    }

    public function getCompleteTasks(User $user){
        $builder = $this->
                    with([
                        'priority',
                        'lead',
                        'responsibleUser'
                    ])->
                    where(function($query) {
                        $query->where([
                                ['is_complete', 1],
                                ['due_date', '>', \DB::raw('DATE_SUB(NOW(), INTERVAL 24 HOUR)')]
                        ]);
                    });

        if(!$user->hasRole('SUPER_ADMINISTRATOR')) {
            $builder->where(function ($query) use ($user) {
                $query->
                where('author_id', $user->id)->
                orWhere('responsible_id', $user->id);
            });
        }//если юзер менеджер, то он будет видеть свои задачи, если админ, то все задачи

        return $builder->
                orderBy('priority_id')->
                get();
    }

    public function getArchives(User $user) {
        $date = Carbon::now()->startOfDay()->subDay(1);
        var_dump($date->toDateTimeString());
        $builder = $this->
                with([
                    'priority',
                    'lead',
                    'responsibleUser'
                ])->
                where(function($query) use($date) {
                    $query->
                        where('due_date', '<', $date)->
                        whereBetween('is_complete', [0,1]);
        });

        if(!$user->hasRole('SUPER_ADMINISTRATOR')) {
            $builder->where(function ($query) use ($user) {
                $query->
                where('author_id', $user->id)->
                orWhere('responsible_id', $user->id);
            });
        }

        return $builder->
                    orderBy('due_date', 'desc')->
                    paginate(config('settings.pagination'));

    }

    public function user() {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function lead() {
        return $this->belongsTo(Lead::class);
    }

    public function priority() {
        return $this->belongsTo(Priority::class);
    }

    public function responsibleUser() {
        return $this->belongsTo(User::class, 'responsible_id', 'id');
    }

    public function comments() {
        return $this->hasMany(TaskComment::class);
    }

    public function lastComment() {
        return $this->comments()->
                    where('comment_value', '!=', NULL)->
                    orderBy('id', 'desc')->first();
    }

    public function renderData($load = true) {

        if($load) {
            $this->load(['priority','lead']);
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'priority_id' => $this->priority_id,
            'due_date' => $this->due_date,
            'is_complete' => $this->is_complete,
            'time_to_complete' => $this->time_to_complete,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->created_at->toDateTimeString(),
            'created_at_time' => $this->created_at->timestamp,
            'priority' => [
                'id' => $this->priority->id,
                'title' => $this->priority->title,
                'color' => $this->priority->color,
            ],
            'lead_id' => $this->lead_id,
            'lead' => [
                'id' => $this->lead->id,
                'title' => $this->lead->title,
                'link' => $this->lead->link,
                'phone' => $this->lead->phone,
            ],
            'author' => $this->user->fullname,
            'author_id' => $this->author_id,
            'responsible' => $this->responsibleUser->fullname,
            'responsible_id' => $this->responsible_id,
        ];
    }
}

