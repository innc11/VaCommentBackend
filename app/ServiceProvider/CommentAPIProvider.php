<?php

namespace ServiceProvider;

use Pimple\Container;
use Model\CommentNotificationModel;

class CommentAPIProvider extends ServiceProviderBase
{
    public function onRegisterRule(Container $container)
    {
        self::registerRule('GET',  '/comment', 'getComments');
        self::registerRule('POST', '/comment', 'publishComment');
    }

    public function checkParameters($array, $fields)
    {
        $missings = [];
        foreach ($fields as $f) {
            if (trim($f)) {
                if (!isset($array[$f]) || !$array[$f])
                    $missings[] = $f;
            }
        }
        return $missings;
    }

    public function publishComment($request, $response, $service, $app)
    {
        $container = $app->container;
        $db = $container['database'];
        $mailSystem = $container['mail'];

        // 返回Json格式
        header('Content-Type:application/json;charset=utf-8');

        // 获取请求的body
        $data = json_decode(file_get_contents('php://input'), true);

        // 检查必要的参数
        $missings = self::checkParameters($data, ['key', 'label', 'nick', 'content', 'parent']);

        if(count($missings) > 0) {
            $response->code(403);
            $response->body('缺少参数: '.json_encode($missings));
            return;
        }
        
        if (!DEVELOPMENT_MODE) { // 不在开发模式时需要检查验证码
            if (!isset($_COOKIE['captcha'])) {
                $response->code(403);
                $response->body(json_encode(['reason' => '没有请求过验证码或者Cookie未能上传'], JSON_UNESCAPED_UNICODE));
                return;
            } else if (!isset($data['captcha'])) {
                $response->code(403);
                $response->body(json_encode(['reason' => '未输入验证码'], JSON_UNESCAPED_UNICODE));
                return;
            } else {
                if (md5(strtolower($data['captcha'])) != $_COOKIE['captcha']) {
                    $response->code(403);
                    $response->body(json_encode(['reason' => '验证码不正确'], JSON_UNESCAPED_UNICODE));
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
            'key' => urldecode($data['key']),
            'label' => $data['label'],
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
        $sql = "INSERT INTO 'comments' (key, label, parent, nick, mail, website, content, approved, time, ip, useragent)".
                "VALUES (:key, :label, :parent, :nick, :mail, :website, :content, :approved, :time, :ip, :useragent)";
        $db->prepare($sql)->execute($newComment)->end();


        // 评论通知------------------------------
        $recipientMail = '';
        $recipientName = '';
        $recipientWebsite = '';

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
                $recipientWebsite = $parent['website'];
            }
        } else { // 如果回复的是文章
            // 给博主发邮件
            $recipientMail = MAIL_OWNER_MAIL;
            $recipientName = MAIL_OWNER_NAME;
            $recipientWebsite = MAIL_SITE_URL;
        }

        // 如果有收件人的话就发送
        if ($recipientName && $recipientMail)
        {
            $permalink = MAIL_SITE_URL . $data['key'];

            $params = [
                'time' => time(),
                'content' => $data['content'],
                'author' => [
                    'name' => $data['nick'],
                    'mail' => $data['mail'],
                    'website' => $data['website']
                ],
                'recipient' => [
                    'name' => $recipientName,
                    'mail' => $recipientMail,
                    'website' => $recipientWebsite,
                ],
                'permalink' => $permalink,

                'subject' => MAIL_SUBJECT,
                'purpose' => '评论通知',
                'testMode' => MAIL_TEST_MODE,
            ];

            $params = CommentNotificationModel::FromArray($params);

            call_user_func_array($mailSystem->send, [$params]);
        }

        echo('[]'); // 避免JQ的DataType对不上导致无法执行success回调
    }

    public function getComments($request, $response, $service, $app)
    {
        $container = $app->container;
        $db = $container['database'];
        $pageCapacity = PAGE_CAPACITY;

        // 返回Json格式
        header('Content-Type:application/json;charset=utf-8');

        // 检查必要的参数
        $missings = self::checkParameters($_GET, ['key', 'label']);

        if(count($missings) > 0) {
            $response->code(403);
            $response->body('缺少参数: '.json_encode($missings));
            return;
        }

        // 记录数据
        $cookieKey = 'identity-'.md5($_GET['key']);
        if (!isset($_COOKIE[$cookieKey])) {
            $visitModel = new \Model\VisitModel($_GET['key'], $_GET['label'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
            call_user_func_array($container['analysis']['visit'], [$db, $visitModel]);
        }
        \Utils\Utils::setCookie($cookieKey, '123', time() + PERIOD_AS_NEW_VISITOR);
        

        // 查询数据
        $key = $_GET['key'];
        $pagination = isset($_GET['pagination']) && $_GET['pagination']>=0? $_GET['pagination']:0;

        $sql = "select * from 'comments' where key = :key and parent = 0 order by time desc";
        $rows = $db->prepare($sql)->execute(['key' => $key])->fetchAll();

        $data = [];

        $commentCount = count($rows);
        $start = min($pagination * $pageCapacity, $commentCount);
        $end = min($start + $pageCapacity, $commentCount);

        for ($i=$start; $i<$end; $i++) {
            $row = $rows[$i];
            $data[] = [
                'id' => $row['id'],
                'avatar' => \Utils\Utils::getAvatarByMail($row['mail']),
                'nick' => $row['nick'],
                'website' => $row['mail'] != MAIL_OWNER_MAIL? $row['website']:MAIL_SITE_URL, // 如果是博主就不需要写网站，就算写了也会变成默认站点地址
                'isauthor' => $row['mail'] == MAIL_OWNER_MAIL,
                'authorlabel' => MAIL_OWNER_NAME,
                'useragent' => $row['useragent'],
                'content' => \Smilie\SmilieSystem::showSmilies($row['content']),
                'time' => $row['time'],
                'replies' => self::getRepliesOfComment($db, $row['id']),
            ];
        }

        echo(json_encode([
            'comments' => $data,
            'pages' => intval($commentCount / $pageCapacity) + ($commentCount % $pageCapacity>0? 1: 0),
            'count' => $commentCount
        ]));
    }

    private function getRepliesOfComment($db, $replyId)
    {
        $sql = "select * from 'comments' where parent = :parent order by time desc";
        $replies = $db->prepare($sql)->execute(['parent' => $replyId])->fetchAll();
        $repliesObj = [];
        foreach ($replies as $reply) {
            $repliesObj[] = [
                'id' => $reply['id'],
                'avatar' => \Utils\Utils::getAvatarByMail($reply['mail']),
                'nick' => $reply['nick'],
                'website' => $reply['website'],
                'isauthor' => $reply['mail'] == MAIL_OWNER_MAIL,
                'authorlabel' => MAIL_OWNER_NAME,
                'useragent' => $reply['useragent'],
                'content' => \Smilie\SmilieSystem::showSmilies($reply['content']),
                'time' => $reply['time'],
                'replies' => self::getRepliesOfComment($db, $reply['id']),
            ];
        }

        return $repliesObj;
    }

}

?>