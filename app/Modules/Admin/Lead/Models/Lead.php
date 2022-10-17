<?php

namespace App\Modules\Admin\Lead\Models;

use App\Modules\Admin\LeadComment\Models\LeadComment;
use App\Modules\Admin\Sources\Models\Source;
use App\Modules\Admin\Status\Models\Status;
use App\Modules\Admin\Task\Models\Task;
use App\Modules\Admin\Unit\Models\Unit;
use App\Modules\Admin\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Lead extends Model
{
    use HasFactory;

    CONST DONE_STATUS = 3;

    protected $fillable = [
        'title',
        'link',
        'phone',
        'source_id',
        'user_id',
        'responsible_id',
        'is_express_delivery',
        'is_add_sale',
    ];

    public function source() {
        return $this->belongsTo(Source::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function unit() {
        return $this->belongsTo(Unit::class);
    }

    public function status() {
        return $this->belongsTo(Status::class);
    }

    public function tasks() {
        return $this->hasMany(Task::class);
    }

    public function comments() {
        return $this->hasMany(LeadComment::class);
    }

    public function statuses()
    {
        return $this->belongsToMany(Status::class);
    }

    public function responsibleUser() {
        return $this->belongsTo(User::class, 'responsible_id', 'id');
    }

    public function lastComment() {
        return $this->comments()->
        where('comment_value', '!=', NULL)->
        orderBy('id', 'desc')->first();
    }

    public function getLeads(User $user)
    {
        $builder = $this->
                    with([
                        'source',
                        'status',
                        'responsibleUser'
                    ])->
                    where(function($query) {
                    $query->whereBetween('status_id', [1, 2])->
                    orWhere([
                        ['status_id', 3],
                        ['updated_at', '>', \DB::raw('DATE_SUB(NOW(), INTERVAL 24 HOUR)')]
                    ]);
                });

        if(!$user->hasRole('SUPER_ADMINISTRATOR')) {
            $builder->where(function ($query) use ($user) {
                $query->
                where('user_id', $user->id)->
                orWhere('responsible_id', $user->id);
            });
        }//если юзер менеджер, то он будет видеть свои лиды, если админ, то все лиды


        return $builder->
                    orderBy('created_at')->
                    get();
    }

    public function getNotDoneLeads(User $user)
    {
        $builder = $this->
                        with([
                            'source',
                            'status',
                            'responsibleUser'
                        ])->
                        where(function($query) {
                            $query->whereBetween('status_id', [1, 2]);
                        });

        if(!$user->hasRole('SUPER_ADMINISTRATOR')) {
            $builder->where(function ($query) use ($user) {
                $query->
                where('user_id', $user->id)->
                orWhere('responsible_id', $user->id);
            });
        }//если юзер менеджер, то он будет видеть свои лиды, если админ, то все лиды


        return $builder->
        orderBy('created_at')->
        get();
    }

    public function getLeadsDoneToday(User $user) {
        date_default_timezone_set('Europe/Moscow');
        $todayStart = Carbon::now()->startOfDay();
        $builder = $this->
                    with([
                        'source',
                        'status',
                        'responsibleUser'
                    ])->
                    where(function($query) use($todayStart){
                        $query->where([
                            ['updated_at', '>', $todayStart],
                            ['status_id', 3],
                            ['isQualityLead', 1]
                        ]);
                    });

        if(!$user->hasRole('SUPER_ADMINISTRATOR')) {
            $builder->where(function ($query) use ($user) {
                $query->
                where('user_id', $user->id)->
                orWhere('responsible_id', $user->id);
            });
        }//если юзер менеджер, то он будет видеть свои лиды, если админ, то все лиды


        return $builder->
                    orderBy('created_at')->
                    get();
    }

    public function getArchive()
    {
        return $this->
                with(['source', 'unit', 'status'])->
                where('status_id', self::DONE_STATUS)->
                where('updated_at', '<', \DB::raw('DATE_SUB(NOW(), INTERVAL 24 HOUR)'))->
                orderBy('updated_at', 'DESC')->
                paginate(config('settings.pagination'));
    }

    public function renderData($load = true) {
        if($load) {
            $this->load(['source','status']);
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'phone' => $this->phone,
            'link' => $this->link,
            'count_create' => $this->count_create,
            'isQualityLead' => (bool)$this->isQualityLead,
            'is_express_delivery' => (bool)$this->is_express_delivery,
            'is_add_sale' => (bool)$this->is_add_sale,
            'source_id' => $this->source_id,
            'user_id' => $this->user_id,
            'status_id' => $this->status_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->created_at->toDateTimeString(),
            'lastComment' =>  isset($this->lastComment()->comment_value) ? $this->lastComment()->comment_value : "",
            'created_at_time' => $this->created_at->timestamp,
            'updated_at_time' => $this->updated_at->timestamp,
            'author' => $this->user->fullname,
            'responsible' => $this->responsibleUser->fullname,
            'responsible_id' => $this->responsible_id,
            'source' => [
                'id' => $this->source->id,
                'title' => $this->source->title,
            ],
            'status' => [
                'id' => $this->status->id,
                'title' => $this->status->title,
                'title_ru' => $this->status->title_ru,
            ],
        ];
    }

}
