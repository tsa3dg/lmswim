<?php

namespace App\Util;

class DateUtility {

    public static function getDaysSinceNewYears($year) {
        if($year % 4 ==0 && ($year % 4 == 400 || year % 4 != 100)) {
            return array(0,31,60,91,121,152,182,213,244,274,305,335,366);
        } else {
            return array(0,31,59,90,120,151,181,212,243,273,304,334,365);
        }
    }

    public static function getDaysInMonth($year, $month) {
        $daysSinceNewYears = self::getDaysSinceNewYears($year);
        return $daysSinceNewYears[$month] - $daysSinceNewYears[$month-1];
    }
}


?>