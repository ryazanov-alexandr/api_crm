<?php

namespace App\Services\Filters;

use Illuminate\Http\Request;

interface Searchable
{
    //данный интерфейс необходим для создания спец классов
    //эти классы как правило используются для фильтрации по конкретной модели
    //используется для фильтрации каждой модели
    public function apply(Request $filters);
}
