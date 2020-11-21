<?php

require 'mail.php';

function onCommentPublished($request, $response, $service, $app)
{
    $dev = $app->config->dev; // 是否启用开发模式
    $db = $app->db;

    // 返回Json格式
    header('Content-Type:application/json;charset=utf-8');

    // 获取请求的body
    $data = json_decode(file_get_contents('php://input'), true);

    // 检查必要的参数
    check($data, ['url', 'title', 'nick', 'content', 'parent', $dev?'':'captcha']); // 开发模式下不会检查验证码

    if (!$dev) { // 不在开发模式时需要检查验证码
        if (!isset($_COOKIE['captcha'])) {
            $response->code(403);
            echo(json_encode(['reason' => '没有请求过验证码或者Cookie未能上传'], JSON_UNESCAPED_UNICODE));
            return;
        } else if (!isset($data['captcha'])) {
            $response->code(403);
            echo(json_encode(['reason' => '未输入验证码'], JSON_UNESCAPED_UNICODE));
            return;
        } else {
            if (md5(strtolower($data['captcha'])) != $_COOKIE['captcha']) {
                $response->code(403);
                echo(json_encode(['reason' => '验证码不正确'], JSON_UNESCAPED_UNICODE));
                return;
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

    // 插入评论到数据库里
    $sql = "INSERT INTO 'comments' (url, title, parent, nick, mail, website, content, approved, time, ip, useragent)".
            "VALUES (:url, :title, :parent, :nick, :mail, :website, :content, :approved, :time, :ip, :useragent)";
    $db->prepare($sql)->execute($newComment)->end();


    // 评论通知------------------------------
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
        $recipientMail = $app->config->owner_mail;
        $recipientName = $app->config->owner_nick;
    }

    // 如果有收件人的话就发送
    if ($recipientName && $recipientMail)
    {
        $headers = getAllHttpHeaders();
        $path = explode('=', $_SERVER['QUERY_STRING'])[1];
        $permalink = isset($headers['Origin'])? $headers['Origin'].$path:$app->config->site_url;

        $params = [
            'subject' => $app->config->mail_subject,
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

        sendMail($params, $app->config->smtp_test_mode);
    }

    echo('[]'); // 避免JQ的DataType对不上导致无法执行success回调
}

$router->respond('POST', '/comment', 'onCommentPublished');



?>