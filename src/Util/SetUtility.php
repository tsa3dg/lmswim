<?php

namespace App\Util;

class SetUtility {

    public static function addObjToSet(&$set, $key, $val) {
        if(!array_key_exists($key, $set)) {
            $set[$key] = array($val->toArray());
        } else {
            $set[$key][] = $val->toArray();
        }
    }
}

?>