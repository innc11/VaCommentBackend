<?php

$dev = true;
$pageCapacity = 7; // 每页显示多少评论
$ownerMail = 'aa@bb.com';
$mailSubject = '您在[xxx]的留言有了新的回应';

$siteInfo = [
    'url' => 'https://baidu.com',
    'title' => 'xxx',
    'description' => '回望间，便是光年之遥。眨眼间，已是千年之外',
];

$smtpConfig = [
    'STMPHost'     => 'smtpdm.aliyun.com',
    'SMTPUserName' => 'xx@xx.com',
    'SMTPPassword' => '12345678',
    'SMTPSecure'   => 'ssl', // '' or 'ssl' or 'tls'
    'SMTPPort'     => 465,
    'fromMail'     => 'xx@xx.com', // 发件邮箱
    'fromName'     => 'xxx', // 发件人名字
    'testMode'     => true, // 测试模式不会真的发送邮件（日志还是有的）
    'snapshot'     => true, // 保存最后的邮件内容快照
];

// 浏览统计（单位秒），超过这个时间以后会被视为新的访客
$cookieRefreshPeriod = 120;

header('Content-Type:application/json;charset=utf-8');
header('Access-Control-Allow-Origin: http://127.0.0.1:4000');
// header('Access-Control-Allow-Origin: http://127.0.0.1:9981');
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Credentials: true");

?>