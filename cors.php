<?php

// 处理CORS请求
if ($allowAllCORSRequest)
{
    if (isset(getAllHttpHeaders()['Origin']))
    {
        header('Access-Control-Allow-Origin: '.getAllHttpHeaders()['Origin']);
    } else {
        if (!$dev) {
            httpStatus(403);
            echo(json_encode([
                'reason' => 'not allowed',
            ]));
            exit();
        }
    }
} else {
    header('Access-Control-Allow-Origin: '.$allowCORSHost);
}
header('Content-Type:application/json;charset=utf-8');
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Credentials: true");

?>