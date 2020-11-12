<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $data = json_decode(file_get_contents('php://input'), true);

    if ($dev)
        check($data, ['url', 'title', 'nick', 'content', 'parent']);
    else
        check($data, ['url', 'title', 'nick', 'content', 'parent', 'captcha']);

    if (!$dev)
    {
        if (isset($_SESSION['captcha']))
        {
            if (strtolower($data['captcha']) != strtolower($_SESSION['captcha']))
            {
                httpStatus(403);
                echo(json_encode([
                    'reason' => 'wrong captcha: ' . $data['captcha']
                ]));
                exit();
            }
        } else {
            httpStatus(403);
            echo(json_encode([
                'reason' => 'no captcha'
            ]));
            exit();
        }
    }

    unset($_SESSION['captcha']);
    
    $newComment = [
        'parent' => $data['parent']!=-1? $data['parent']:0,
        'url' => urldecode($data['url']),
        'title' => $data['title'],
        'nick' => $data['nick'],
        'mail' => $data['mail']? $data['mail']:'',
        'website' => $data['website']? $data['website']:'',
        'content' => $data['content'],
        'approved' => true,
        'time' => time(),
        'ip' => $_SERVER['REMOTE_ADDR'],
        'useragent' => $_SERVER['HTTP_USER_AGENT']
    ];

    $sql = "INSERT INTO 'comments' (url, title, parent, nick, mail, website, content, approved, time, ip, useragent)".
            "VALUES (:url, :title, :parent, :nick, :mail, :website, :content, :approved, :time, :ip, :useragent)";
    $statement = $pdo->prepare($sql);
    $statement->execute($newComment);
    $statement->closeCursor();

    // 评论通知 
    if ($data['mail'])
    {
        $recipient = '';

        // 如果是回复的另一条评论
        if ($data['parent']!=-1)
        {
            $sql = "select * from 'comments' where id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute(['id' => $data['parent']]);
            $parent = $statement->fetch();
            $statement->closeCursor();

            // 被回复者需要有邮箱才可以回复
            if ($parent['mail'])
            {
                // 不是回复自己的评论时才需要回复
                if ($parent['mail'] != $data['mail'])
                {
                    $recipient = $data['mail'];
                }
            }
        } else { // 如果回复的是文章
            $recipient = $ownerMail;
        }

        $params = [
            'subject' => $mailSubject,
            'comment' => [
                'time' => date('Y-n-j H-i-s', time()),
                'text' => $data['content'],
                'author' => $data['nick'],
                'mail' => 'authorMail',
                'permalink' => isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER']:$siteInfo['url'],
            ],
            'recipients' => [[
                'mail' => $recipient,
                'name' => $data['nick']
            ]],
            'tag' => '评论通知'
        ];

        sendMail($smtpConfig, $siteInfo, $params, $smtpConfig['testMode']);
    } else {
        echo('不需要发邮件');
    }
}

?>