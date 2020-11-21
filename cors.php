<?php

// 处理CORS请求
if ($config->allow_all_cors_request) {
    if (isset(getAllHttpHeaders()['Origin'])) {
        header('Access-Control-Allow-Origin: '.getAllHttpHeaders()['Origin']);
    }
} else {
    header('Access-Control-Allow-Origin: '.$config->allow_cors_host);
}
header("Access-Control-Allow-Methods: ".$_SERVER['REQUEST_METHOD']);
header("Access-Control-Allow-Credentials: true");

?>