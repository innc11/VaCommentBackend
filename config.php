<?php

$dev = false;
$pageCapacity = 7; // 每页显示多少评论
$ownerMail = 'aa@bb.cn';
$mailSubject = '您在[xx]的留言有了新的回应';
$allowAllCORSRequest = true; // 是否智能响应CORS请求（一般情况建议关闭）
$allowCORSHost = 'http://127.0.0.1:4001'; // CORS允许的站点($allowAllCORSRequest为false时有效)

$siteInfo = [
    'url' => 'https://xx.cn',
    'title' => 'xx',
    'description' => '回望间，便是光年之遥。眨眼间，已是千年之外',
];

$smtpConfig = [
    'STMPHost'     => 'smtpdm.aliyun.com',
    'SMTPUserName' => 'xx@com',
    'SMTPPassword' => '',
    'SMTPSecure'   => 'ssl', // '' or 'ssl' or 'tls'
    'SMTPPort'     => 465,
    'fromMail'     => 'xx@com', // 发件邮箱
    'fromName'     => 'xx', // 发件人名字
    'testMode'     => false, // 测试模式不会真的发送邮件（日志还是有的）
    'snapshot'     => true, // 保存最后的邮件内容快照
];

// 浏览统计（单位秒），超过这个时间以后会被视为新的访客
$cookieRefreshPeriod = 10;

?>