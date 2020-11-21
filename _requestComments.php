<?php

function getReply($db, $config, $replyId)
{
    $sql = "select * from 'comments' where parent = :parent order by time desc";
    $replies = $db->prepare($sql)->execute(['parent' => $replyId])->fetchAll();
    $repliesObj = [];
    foreach ($replies as $reply) {
        $repliesObj[] = [
            'id' => $reply['id'],
            'avatar' => getAvatarByMail($reply['mail']),
            'nick' => $reply['nick'],
            'website' => $reply['website'],
            'isauthor' => $reply['mail']==$config->owner_mail,
            'useragent' => $reply['useragent'],
            'content' => showsmilies($config, $reply['content']),
            'time' => $reply['time'],
            'replies' => getReply($db, $config, $reply['id']),
        ];
    }

    return $repliesObj;
}


function visit($db, $config, $expires)
{
    $cookieKey = 'identity-'.md5($_GET['url']);

    if (!isset($_COOKIE[$cookieKey])) {
        $sql = "INSERT INTO 'views' (url, title, time, ip, useragent)".
                "VALUES (:url, :title, :time, :ip, :useragent)";
        $db->prepare($sql)->execute([
            'url' => $_GET['url'],
            'title' => $_GET['title'],
            'time' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'useragent' => $_SERVER['HTTP_USER_AGENT']
        ])->end();
    }

    setCookie2($cookieKey, '123', ['expires' => time()+$expires]);
}


function onCommentsRequested($request, $response, $service, $app)
{
    $config = $app->config;
    $db = $app->db;
    $pageCapacity = $config->page_capacity;
    $ownerMail = $config->owner_mail;
    $siteUrl = $config->site_url;

    // 返回Json格式
    header('Content-Type:application/json;charset=utf-8');

    check($_GET, ['url', 'title']);

    visit($db, $config, $config->cookie_refresh_period);

    $url = $_GET['url'];
    $pagination = isset($_GET['pagination']) && $_GET['pagination']>=0? $_GET['pagination']:0;

    $sql = "select * from 'comments' where url = :url and parent = 0 order by time desc";
    $rows = $db->prepare($sql)->execute(['url' => $url])->fetchAll();

    $data = [];

    $commentCount = count($rows);
    $start = min($pagination * $pageCapacity, $commentCount);
    $end = min($start + $pageCapacity, $commentCount);

    for ($i=$start; $i<$end; $i++) {

        $row = $rows[$i];
        
        $data[] = [
            'id' => $row['id'],
            'avatar' => getAvatarByMail($row['mail']),
            'nick' => $row['nick'],
            'website' => $row['mail'] != $ownerMail? $row['website']:$siteUrl, // 如果是博主就不需要写网站，就算写了也会变成默认站点地址
            'isauthor' => $row['mail'] == $ownerMail,
            'useragent' => $row['useragent'],
            'content' => showsmilies($config, $row['content']),
            'time' => $row['time'],
            'replies' => getReply($db, $config, $row['id']),
        ];
    }

    echo(json_encode([
        'comments' => $data,
        'pages' => intval($commentCount / $pageCapacity) + ($commentCount % $pageCapacity>0? 1: 0),
        'count' => $commentCount
    ]));
}
    
$router->respond('GET', '/comment', 'onCommentsRequested');


?>