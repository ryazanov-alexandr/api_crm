<?php

namespace App\Services\Filters;

use Illuminate\Database\Eloquent\Builder;

interface Filterable
{
    //реализация каждого отдельного фильтра
    //имплементирует все фильтры, которые будут созданы
    //будет использоваться для реализации всех отдельных фильтров
    public function apply(Builder $builder, $value);
}
