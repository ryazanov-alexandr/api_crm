<?php

namespace App\Services\Date;

class DateCheck
{
    public static function isValid($str_date, $str_dateformat = "Y-m-d") : bool {
        $date = \DateTime::createFromFormat($str_dateformat, $str_date);

        if($date && (int)$date->format("Y") < 1900) {
            return false;
        }

        return $date && \DateTime::getLastErrors()["warning_count"] == 0 && \DateTime::getLastErrors()["error_count"] == 0;
    }
}
