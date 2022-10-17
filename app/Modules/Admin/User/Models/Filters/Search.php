<?php

namespace App\Modules\Admin\User\Models\Filters;

use App\Services\Filters\Filterable;
use Illuminate\Database\Eloquent\Builder;

class Search implements Filterable
{

    public function apply(Builder $builder, $value)
    {
        return $builder->where(function($query) use($value) {
            $query->where('firstname', 'like', '%' . $value . '%')
                ->orWhere('lastname', 'like', '%' . $value . '%')
                ->orWhere('email', 'like', '%' . $value . '%')
                ->orWhere('phone', 'like', '%' . $value . '%');
        });
    }
}
