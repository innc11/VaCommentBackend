<?php

namespace Mail;

use Model\CommentNotificationModel;
use PHPMailer\PHPMailer\PHPMailer;

class MailClient
{
    private static function log(string $content, string $linebreak=PHP_EOL)
    {
        $r = file_put_contents(MAIL_LOG_FILE, sprintf("[%s]: %s%s", date('Y-m-d H:i:s'), $content, $linebreak), FILE_APPEND);

        if ($r) {
            echo $r===false;
        }
    }

    private static function readMailTemplate()
    {
        if (!file_exists(MAIL_TEMPLATE_FILE)) {
            return '模板文件' . MAIL_TEMPLATE_FILE . '不存在';
        }

        return file_get_contents(MAIL_TEMPLATE_FILE);
    }

    public static function sendMail(CommentNotificationModel $notification)
    {
        if (!isset($notification->recipient))
            return; // 没有收信人
            
        if (empty($notification->subject))
            return; // 没有邮件主题(标题)

        // if (empty($notification->recipients))
        //     return; // 没有收信人
    
        $mail = new PHPMailer(true);
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->Encoding = PHPMailer::ENCODING_BASE64;
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;         // SMTP 服务地址
        $mail->SMTPAuth = true;          // 开启认证
        $mail->Username = SMTP_USERNAME; // SMTP 用户名
        $mail->Password = SMTP_PASSWORD; // SMTP 密码
        $mail->SMTPSecure = SMTP_SECURE; // SMTP 加密类型 'ssl' or 'tls'.
        $mail->Port = SMTP_PORT;         // SMTP 端口
        $mail->setFrom(SMTP_FROM_MAIL, SMTP_FROM_NAME); // 发件人信息
        $mail->Subject = $notification->subject;       // 邮件标题
        $mail->isHTML();                 // 邮件为HTML格式
        $mail->Body = self::getMailBody($notification);

        // foreach ($notification->recipients as $recipient) 
        //     $mail->addAddress($recipient['mail'], $recipient['name']); // 收件人

        $mail->addAddress($notification->recipient->mail, $notification->recipient->name); // 收件人

        // 测试模式不需要真的发送邮件
        if(!$notification->testMode)
            $mail->send();

        // 保存邮件内容快照
        if(MAIL_SNAPSHOT_ENABLE)
            file_put_contents(MAIL_SNAPSHOT_FILE, $mail->Body);

        // 记录日志
        if ($mail->isError()) {
            $data = $mail->ErrorInfo;
        } else {
            $data = $notification->purpose.'已发送: ';

            // foreach ($notification->recipients as $recipient) {
            //     $data .= $recipient['name'].'('.$recipient['mail'].') ';
            // }

            $data .= $notification->recipient->name.'('.$notification->recipient->mail.') ';

            if($notification->testMode)
                $data .= "  [测试模式]";
        }
        
        self::log($data);
    }

    public static function getMailBody(CommentNotificationModel $notification)
    {
        $table = [
            '{SiteUrl}',            MAIL_SITE_URL,
            '{SiteTitle}',          MAIL_SITE_TITLE,
            '{SiteDescription}',    MAIL_SITE_SUBTITLE,
            '{Subject}',            $notification->subject,
            '{CommentText}',        \Smilie\SmilieSystem::showSmilies(htmlspecialchars($notification->content)),

            '{AuthorName}',         htmlspecialchars($notification->author->name),
            '{AuthorMail}',         htmlspecialchars($notification->author->mail),
            '{AuthorWebsite}',      urldecode(htmlspecialchars($notification->author->website)),

            '{RecipientName}',      htmlspecialchars($notification->recipient->name),
            '{RecipientMail}',      htmlspecialchars($notification->recipient->mail),
            '{RecipientWebsite}',   urldecode(htmlspecialchars($notification->recipient->website)),
            
            '{Permalink}',          urldecode(htmlspecialchars($notification->permalink)), // 文章链接
            '{Time}',               $notification->getFormatedDateTime(), // 格式化过的
            '{RawTime}',            $notification->time, // 时间戳
        ];

        $search = [];
        $replace = [];

        for($i=0;$i<count($table)/2;$i++) {
            $search[] = $table[$i*2];
            $replace[] = $table[$i*2 + 1];
        }

        return str_replace($search, $replace, self::readMailTemplate());
    }
}


?>