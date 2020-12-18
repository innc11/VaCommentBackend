<?php

namespace Cors;

class CorsHandle
{
    public static function call()
    {
        if (CORS_ALLOW_ALL) {
            $httpHeaders = \Utils\Utils::getAllHttpHeaders();
            if (isset($httpHeaders['Origin'])) {
                header('Access-Control-Allow-Origin: '.$httpHeaders['Origin']);
            }
        } else {
            header('Access-Control-Allow-Origin: '.CORS_ALLOW_HOST);
        }

        header("Access-Control-Allow-Methods: ".$_SERVER['REQUEST_METHOD']);
        header("Access-Control-Allow-Credentials: true");
    }
}

?>