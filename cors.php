<?php

// 处理CORS请求
if ($config->allow_all_cors_request) {
    if (isset(getAllHttpHeaders()['Origin'])) {
        header('Access-Control-Allow-Origin: '.getAllHttpHeaders()['Origin']);
    } else {
        if (!$config->dev) {
            httpStatus(403);
            echo(json_encode(['reason' => 'not allowed',]));
            exit();
        }
    }
} else {
    header('Access-Control-Allow-Origin: '.$config->allow_cors_host);
}
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Credentials: true");

?>