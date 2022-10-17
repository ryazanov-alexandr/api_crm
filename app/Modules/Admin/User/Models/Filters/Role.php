<?php

namespace App\Modules\Admin\User\Models\Filters;

use App\Services\Filters\Filterable;
use Illuminate\Database\Eloquent\Builder;

class Role implements Filterable
{

    public function apply(Builder $builder, $value)
    {
        //выбираем юзеров, у которых есть связь с ролью
        return $builder->whereHas('roles', function ($query) use($value) {
            $query->where('role_id', $value);
        });
    }
}
