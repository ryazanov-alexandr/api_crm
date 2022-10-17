<?php

namespace App\Services\Date\Facade;

use Illuminate\Support\Facades\Facade;

class DateService extends Facade
{

    protected static function getFacadeAccessor()
    {
        //обращаясь фасаду DateService мы будем напрямую обращаться к ячейке dateCheck
        //а в этой ячейке находится объект класса DateCheck
        return 'dateCheck';
    }

}
