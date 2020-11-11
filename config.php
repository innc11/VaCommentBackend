<?php

$dev = false;
$ownerMail = 'aa@bb.cc';
$mailSubject = '您在百度的留言有了新的回应';

$siteInfo = [
    'url' => 'https://baidu.com',
    'title' => '百度',
    'description' => '百度百度百度百度',
];

$smtpConfig = [
    'STMPHost'     => 'smtpdm.aliyun.com',
    'SMTPUserName' => '',
    'SMTPPassword' => '',
    'SMTPSecure'   => 'ssl', // '' or 'ssl' or 'tls'
    'SMTPPort'     => 465,
    'fromMail'     => '', // 发件邮箱
    'fromName'     => '百度', // 发件人名字
    'testMode'     => false, // 测试模式不会真的发送邮件（日志还是有的）
    'snapshot'     => false, // 保存最后的邮件内容快照
];

// 浏览统计（单位秒），超过这个时间以后会被视为新的访客
$cookieRefreshPeriod = 120;

?>