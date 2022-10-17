<?php

namespace App\Modules\Admin\TaskComment\Models;

use App\Modules\Admin\Lead\Models\Lead;
use App\Modules\Admin\Priority\Models\Priority;
use App\Modules\Admin\Task\Models\Task;
use App\Modules\Admin\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    //
    protected $fillable = [
        'text',
        'comment_value',
    ];


    public function lead() {
        return $this->belongsTo(Lead::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

}
