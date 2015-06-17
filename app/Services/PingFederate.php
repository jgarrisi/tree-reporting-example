<?php namespace App\Services;

class PingFederate {

    public static function getUsername() {
        if (array_key_exists('PF_AUTH_UID', $_SERVER)) {
            return $_SERVER['PF_AUTH_UID'];
        } else {
            return env('NO_PING_USER', 'anonymous');
        }
    }

}
