<?php namespace App\Services;

use Config;

class RIPSvc {

    public static function getAppInfo($EPRId) {
        $url = Config::get('services.RIPSvc.baseurl') . 'getAppInfo';
        $url .= "?EPRId=" . urlencode($EPRId);

        return RIPSvc::httpRequestBasic($url);
    }

    private static function httpRequestBasic($url, $postData = null) {
        $ch = \curl_init($url);

        \curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        \curl_setopt($ch, CURLOPT_USERPWD, Config::get('services.RIPSvc.baseurl'));
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        \curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        \curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        \curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        if($postData != null) {
            \curl_setopt($ch, CURLOPT_POST, 1);
            \curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $response = \curl_exec($ch);
        $http_status = \curl_getinfo($ch, CURLINFO_HTTP_CODE);
        \curl_close($ch);

        if($http_status !== 200) {
            $response = false;
        }

        return $response;
    }
}
