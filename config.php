<?php

$dev = false;
$pageCapacity = 7; // 每页显示多少评论
$ownerMail = 'pm@innc11.cn';
$mailSubject = '您在[夜街尘]的留言有了新的回应';

$siteInfo = [
    'url' => 'https://innc11.cn',
    'title' => '夜街尘',
    'description' => '回望间，便是光年之遥。眨眼间，已是千年之外',
];

$smtpConfig = [
    'STMPHost'     => 'smtpdm.aliyun.com',
    'SMTPUserName' => 'notification@notify.innc11.cn',
    'SMTPPassword' => 'fH234d3756gaGhJgjg7j',
    'SMTPSecure'   => 'ssl', // '' or 'ssl' or 'tls'
    'SMTPPort'     => 465,
    'fromMail'     => 'notification@notify.innc11.cn', // 发件邮箱
    'fromName'     => '夜街尘1', // 发件人名字
    'testMode'     => false, // 测试模式不会真的发送邮件（日志还是有的）
    'snapshot'     => true, // 保存最后的邮件内容快照
];

// 浏览统计（单位秒），超过这个时间以后会被视为新的访客
$cookieRefreshPeriod = 120;

header('Content-Type:application/json;charset=utf-8');
// header('Access-Control-Allow-Origin: https://blog.innc11.cn');
header('Access-Control-Allow-Origin: http://127.0.0.1:4000');
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Credentials: true");

?>