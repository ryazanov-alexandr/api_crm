<?php

namespace App\Modules\Admin\User\Models\Filters;

use App\Modules\Admin\User\Models\User;
use App\Services\Filters\BaseSearch;
use App\Services\Filters\Searchable;

class UserSearch implements Searchable
{
    CONST MODEL = User::class;
    use BaseSearch;
}
