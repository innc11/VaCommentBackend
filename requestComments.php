<?php

function getReply($pdo, $replyId)
{
    $sql = "select * from 'comments' where parent = :parent";
    $statement = $pdo->prepare($sql);
    $statement->execute(['parent' => $replyId]);
    $replies = $statement->fetchAll();
    $statement->closeCursor();

    $repliesObj = [];

    foreach ($replies as $reply) {
        $repliesObj[] = [
            'id' => $reply['id'],
            'avatar' => getAvatarByMail($reply['mail']),
            'nick' => $reply['nick'],
            'website' => $reply['website'],
            'useragent' => $reply['useragent'],
            'content' => $reply['content'],
            'time' => $reply['time'],
            'replies' => getReply($pdo, $reply['id']),
        ];
    }

    return $repliesObj;
}


function visit($pdo, $expires)
{
    $cookieKey = 'identity-'.md5($_SERVER['HTTP_REFERER']);

    if (!isset($_COOKIE[$cookieKey]))
    {
        $sql = "INSERT INTO 'views' (url, time, ip, useragent)".
                "VALUES (:url, :time, :ip, :useragent)";
        $statement = $pdo->prepare($sql);
        $statement->execute([
            'url' => $_SERVER['HTTP_REFERER'],
            'time' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'useragent' => $_SERVER['HTTP_USER_AGENT']
        ]);
        $statement->closeCursor();
    }

    
    setcookie($cookieKey, '123', time()+$expires);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
    check($_GET, ['url']);

    visit($pdo, $cookieRefreshPeriod);

    $url = $_GET['url'];
    $pagination = isset($_GET['pagination']) && $_GET['pagination']>=0? $_GET['pagination']:0;
    $pageCapacity = 3;

    $sql = "select * from 'comments' where url = :url and parent is NULL";
    $statement = $pdo->prepare($sql);
    $statement->execute(['url' => $url]);
    $rows = $statement->fetchAll();
    $statement->closeCursor();

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
            'website' => $row['website'],
            'useragent' => $row['useragent'],
            'content' => $row['content'],
            'time' => $row['time'],
            'replies' => getReply($pdo, $row['id']),
        ];
    }

    

    echo(json_encode([
        'comments' => $data,
        'pages' => intval($commentCount / $pageCapacity) + ($commentCount % $pageCapacity>0? 1: 0),
        'count' => $commentCount
    ]));

}
    

?>