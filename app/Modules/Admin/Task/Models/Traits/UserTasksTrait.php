<?php

namespace App\Modules\Admin\Task\Models\Traits;

use App\Modules\Admin\Task\Models\Task;

trait UserTasksTrait
{
    public function authorTasks() {
        return $this->hasMany(Task::class, 'author_id', 'id');
    }

    public function responsibleTasks() {
        return $this->hasMany(Task::class, 'responsible_id', 'id');
    }
}
