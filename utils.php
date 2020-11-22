<?php

function getAvatarByMail($mail)
{
    global $config;
    $paragmeters = [
        $config->gravatar_nocache? 'f=y':'',
        'd='.$config->gravatar_default,
    ];
    return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($mail))).'?'.implode('&', $paragmeters);
}

function check($array, $fields)
{
    foreach ($fields as $f) {
        if (trim($f)) {
            if (!isset($array[$f]) || !$array[$f])
            {
                httpStatus(403);
                echo(json_encode([
                    'reason' => 'missing '.$f
                ]));
                exit();
            }
        }
    }
}

// https://blog.csdn.net/ahaotata/article/details/84999015
function getAllHttpHeaders()
{
    foreach ($_SERVER as $name => $value)
    {
        if (substr($name, 0, 5) == 'HTTP_')
        {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
}

function setCookie2($key, $value, $paragmeters=[])
{
    global $config;
    $cfg = [];

    if ($config->ssl) {
        $cfg = [
            'expires' => 0,
            'samesite' => 'None', // 'samesite' => 'None' // None || Lax  || Strict
            'secure' => true,
        ];
    } else {
        $cfg = [
            'expires' => 0,
        ];
    }

    array_merge($cfg, $paragmeters);
    
    setcookie($key, $value, $cfg);
}

?>