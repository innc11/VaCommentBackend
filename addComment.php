<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $data = json_decode(file_get_contents('php://input'), true);

    if ($dev)
        check($data, ['url', 'nick', 'content', 'parent']);
    else
        check($data, ['url', 'nick', 'content', 'parent', 'captcha']);

    if (!$dev)
    {
        if (isset($_SESSION['captcha']))
        {
            if (strtolower($data['captcha']) != strtolower($_SESSION['captcha']))
            {
                echo(json_encode([
                    'reason' => 'wrong captcha: ' . $data['captcha']
                ]));
                http_response_code(403);
                return;
            }
        } else {
            echo(json_encode([
                'reason' => 'no captcha'
            ]));
            http_response_code(501);
            return;
        }
    }

    unset($_SESSION['captcha']);
    
    $newComment = [
        'parent' => $data['parent']!=-1? $data['parent']:NULL,
        'url' => $data['url'],
        'nick' => $data['nick'],
        'mail' => $data['mail']? $data['mail']:NULL,
        'website' => $data['website']? $data['website']:NULL,
        'content' => $data['content'],
        'approved' => true,
        'time' => time(),
        'ip' => $_SERVER['REMOTE_ADDR'],
        'useragent' => $_SERVER['HTTP_USER_AGENT']
    ];

    $sql = "INSERT INTO 'comments' (url, parent, nick, mail, website, content, approved, time, ip, useragent)".
            "VALUES (:url, :parent, :nick, :mail, :website, :content, :approved, :time, :ip, :useragent)";
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
                'time' => time(),
                'text' => $data['content'],
                'author' => $data['nick'],
                'mail' => 'authorMail',
                'permalink' => 'https://baidu.com',
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