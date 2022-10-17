<?php

namespace App\Modules\Admin\Priority\Models;

use App\Modules\Admin\Task\Models\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'color',
    ];

    public function tasks() {
        return $this->hasMany(Task::class);
    }
}
