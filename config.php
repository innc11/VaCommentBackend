<?php

// 配置信息
$config2 = [
    'version' => 1, // 版本信息不要更改
    'dev' => false, // 开发模式下不检查验证码
    'page_capacity' => 5, // 每页显示多少评论
    'allow_all_cors_request' => true, // 是否响应所有CORS请求（考虑到安全问题建议关闭）
    'allow_cors_host' => 'http://127.0.0.1:4001', // CORS允许的站点(allow_all_cors_request为false时有效)
    'owner_mail' => 'pm@xxx.com', // 博主邮箱，用于提示博主和现实作者标识
    'owner_nick' => '博主', // 博主称呼，主要用于logs文件里的显示（可以保持默认）
    'mail_subject' => '您在[夜街尘]的留言有了新的回应', // 邮件主题/标题
    'mail_template_filename' => 'template.html', // 邮件模板文件
    'mail_logs_filename' => 'log.txt', // logs文件
    'mail_snapshot_filename' => 'snapshot.html', // 邮件快照文件
    'site_url' => 'https://xx.cn', // 站点URL，用于邮件内的超链接
    'site_title' => '夜街尘', // 站点名
    'site_description' => '回望间，便是光年之遥。眨眼间，已是千年之外', // 站点描述
    'smtp_host'     => 'smtpdm.aliyun.com', // smtp服务器地址
    'smtp_username' => 'xx@xx.cn', // smtp用户名
    'smtp_password' => 'F65FG8g87fgg3', // smtp密码
    'smtp_secure'   => 'ssl', // 加密方式，'' or 'ssl' or 'tls' 
    'smtp_port'     => 465, // smtp端口
    'smtp_from_mail' => 'xx@xx.cn', // 发件邮箱（通常需要和smtp_username一致）
    'smtp_from_name' => '夜街尘1', // 发件人名字（对方邮箱里的'发件人'名字）
    'smtp_test_mode' => false, // 测试模式不会真的发送邮件（日志还是有的）
    'smtp_snapshot' => false, // 保存最后的邮件内容快照（仅供调试）
    'cookie_refresh_period' => 600, // 浏览统计（单位秒），超过这个时间以后会被视为新的访客
];

class ConfigTool
{
    public function __get($name) 
    {
        global $config2;
        return $config2[$name];
    }
}

$config = new ConfigTool();


?>