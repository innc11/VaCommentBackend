<?php

namespace Cors;

class CorsHandle
{
    public static function call()
    {
        if (CORS_ALLOW_ALL) {
            $httpHeaders = \Utils\Utils::httpHeaders();
            if (isset($httpHeaders['Origin'])) {
                header('Access-Control-Allow-Origin: '.$httpHeaders['Origin']);
            }
        } else {
            header('Access-Control-Allow-Origin: '.CORS_ALLOW_HOST);
        }

        header("Access-Control-Allow-Methods: ".$_SERVER['REQUEST_METHOD']);
        header("Access-Control-Allow-Credentials: true");

        // 被允许的响应中的自定义headers(在CORS场景中)
        header("Access-Control-Expose-Headers: ".implode(', ', ['X-Extra-Message', 'X-Token-Renew']));
    }
}

?>