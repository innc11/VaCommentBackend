<?php

require dirname(__FILE__) . '/PHPMailer/src/PHPMailer.php';
require dirname(__FILE__) . '/PHPMailer/src/SMTP.php';
require dirname(__FILE__) . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function mailBody($comment, $siteInfo, $subject)
{
    $commentAt = $comment['time'];
    $commentText = htmlspecialchars($comment['text']);
    $content = getTemplate();

    $search  = array(
        '{siteUrl}',
        '{siteTitle}',
        '{siteDescription}',
        '{subject}',
        '{commentText}',
        '{commentAuthor}',
        '{authorMail}',
        '{permalink}',
        '{time}'
    );
    
    $replace = array(
        $siteInfo['url'],
        $siteInfo['title'],
        $siteInfo['description'],
        $subject,
        $commentText,
        $comment['author'],
        $comment['mail'],
        $comment['permalink'],
        $commentAt
    );

    $text = str_replace($search, $replace, $content);
    
    return $text;
}

function sendMail($smtpConfig, $siteInfo, $params, $testMode=false)
{
    $subject = $params['subject'];
    $comment = $params['comment'];
    $recipients = $params['recipients'];
    $tag = $params['tag'];
    
    if (empty($recipients))
        return; // 没有收信人

    try {
        $mail = new PHPMailer(true);
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->Encoding = PHPMailer::ENCODING_BASE64;
        $mail->isSMTP();
        $mail->Host = $smtpConfig['STMPHost']; // SMTP 服务地址
        $mail->SMTPAuth = true; // 开启认证
        $mail->Username = $smtpConfig['SMTPUserName']; // SMTP 用户名
        $mail->Password = $smtpConfig['SMTPPassword']; // SMTP 密码
        $mail->SMTPSecure = $smtpConfig['SMTPSecure']; // SMTP 加密类型 'ssl' or 'tls'.
        $mail->Port = $smtpConfig['SMTPPort']; // SMTP 端口
        $mail->setFrom($smtpConfig['fromMail'], $smtpConfig['fromName']);
        $mail->Subject = $subject; // 邮件标题
        $mail->isHTML(); // 邮件为HTML格式

        foreach ($recipients as $recipient) 
            $mail->addAddress($recipient['mail'], $recipient['name']); // 发件人

        // 邮件内容
        $content = mailBody($comment, $siteInfo, $subject);
        $mail->Body = $content;

        // 测试模式不需要真的发送邮件
        if(!$testMode)
            $mail->send();

        // 保存邮件内容快照
        if($smtpConfig['snapshot'])
        {
            $fileName = dirname(__FILE__) . '/Snapshot.html';
            file_put_contents($fileName, $content);
        }

        // 记录日志
        if ($mail->isError()) {
            $data = $mail->ErrorInfo; // 记录发信失败的日志
        } else { // 记录发信成功的日志
            $data  = '邮件已发送! ';
            $data .=  $smtpConfig['fromName'] . '(' . $smtpConfig['fromMail'] . ')';
            $data .= ' To ';

            foreach ($recipients as $recipient) {
                $recipientName = $recipient['name'];
                $recipientMail = $recipient['mail'];
                
                $data .= $recipientName . '(' . $recipientMail . '), ';
            }

            $data .= "  (" . $tag . ")"; // 评论通知 / 评论审核

            if($testMode)
                $data .= "  (测试模式)";
        }
        
        mail_log($data);
    } catch (Exception $e) {
        mail_log($str);
        mail_log($e);
    }
}

function getTemplate()
{
    $template = 'mail-template.html';
    $templatefile = dirname(__FILE__) . '/' . $template;

    if (!file_exists($templatefile)) {
        return '模板文件' . $template . '不存在';
    }

    return file_get_contents($templatefile);
}

function mail_log($content, $linebreak=PHP_EOL)
{
    $logFile = dirname(__FILE__) . '/log.txt';
    file_put_contents($logFile, sprintf("[%s]: %s%s", date('Y-m-d H:i:s'), $content, $linebreak), FILE_APPEND);
}

?>