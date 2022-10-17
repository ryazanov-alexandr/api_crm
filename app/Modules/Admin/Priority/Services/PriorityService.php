<?php

namespace App\Modules\Admin\Priority\Services;

use App\Modules\Admin\Priority\Models\Priority;
use Illuminate\Http\Request;

class PriorityService
{

    public function getPriority()
    {
        return Priority::all();
    }


}
