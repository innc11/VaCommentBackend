<?php

function getAvatarByMail($mail)
{
    return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($mail)));
}

function check($array, $fields)
{
    foreach ($fields as $f) {
        if (!isset($array[$f]) || !$array[$f])
        {
            echo(json_encode([
                'reason' => 'missing '.$f
            ]));
            http_response_code(403);
            exit();
        }
    }
}

?>