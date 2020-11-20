<?php

$dev = $config->dev; // 是否启用开发模式

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    // 获取请求的body
    $data = json_decode(file_get_contents('php://input'), true);

    if ($dev) // 开发模式下不会检查验证码
        check($data, ['url', 'title', 'nick', 'content', 'parent']);
    else
        check($data, ['url', 'title', 'nick', 'content', 'parent', 'captcha']);

    if (!$config->dev) { // 不在开发模式时需要检查验证码
        if (!isset($_COOKIE['captcha'])) {
            httpStatus(403);
            echo(json_encode(['reason' => '没有请求过验证码'], JSON_UNESCAPED_UNICODE));
            exit();
        } else if (!isset($data['captcha'])) {
            httpStatus(403);
            echo(json_encode(['reason' => '未输入验证码'], JSON_UNESCAPED_UNICODE));
            exit();
        } else {
            if (md5(strtolower($data['captcha'])) != $_COOKIE['captcha'])
            {
                httpStatus(403);
                echo(json_encode(['reason' => '验证码不正确'], JSON_UNESCAPED_UNICODE));
                exit();
            }
        }
    }

    // 销毁验证码
    if (isset($_SESSION) && isset($_SESSION['captcha']))
        unset($_SESSION['captcha']);
    
    // 准备插入新的评论
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
    $db->prepare($sql)->execute($newComment)->end();

    // 评论通知 部分
    $recipientMail = '';
    $recipientName = '';

    // 如果是回复的一条评论而不是文章
    if ($data['parent']!=-1)
    {
        $sql = "select * from 'comments' where id = :id";
        $parent = $db->prepare($sql)->execute(['id' => $data['parent']])->fetch(); // 查找父评论

        // 被回复者需要有邮箱才可以回复 而且 不是回复自己的评论时才需要回复
        if ($parent['mail'] && $parent['mail'] != $data['mail'])
        {
            // 给被回复者发邮件
            $recipientMail = $parent['mail'];
            $recipientName = $parent['nick'];
        }
    } else { // 如果回复的是文章
        // 给博主发邮件
        $recipientMail = $config->owner_mail;
        $recipientName = $config->owner_nick;
    }

    // 如果有收件人的话就发送
    if ($recipientName && $recipientMail)
    {
        $headers = getAllHttpHeaders();
        $path = explode('=', $_SERVER['QUERY_STRING'])[1];
        $permalink = isset($headers['Origin'])? $headers['Origin'].$path:$config->site_url;
    
        $params = [
            'subject' => $config->mail_subject,
            'comment' => [
                'time' => date('Y-n-j H-i-s', time()),
                'text' => $data['content'],
                'author' => $data['nick'],
                'mail' => 'authorMail',
                'permalink' => $permalink,
            ],
            'recipients' => [[
                'mail' => $recipientMail,
                'name' => $recipientName,
            ]],
            'tag' => '评论通知'
        ];
    
        sendMail($params, $config->smtp_test_mode);
    }

}

?>