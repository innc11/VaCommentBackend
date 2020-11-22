<?php

// 配置信息
$config =  (object) [
    'version' => 2, // 版本信息不要更改
    'dev' => false, // 开发模式下不检查验证码
    'ssl' => isset($_SERVER['HTTPS']),
    'page_capacity' => 5, // 每页显示多少评论
    'allow_all_cors_request' => true, // 是否响应所有CORS请求（考虑到安全问题建议调试好后关闭）
    'allow_cors_host' => 'http://127.0.0.1:4001', // CORS允许的站点(allow_all_cors_request为false时有效)
    'owner_mail' => 'pm@ xxx.com', // 博主邮箱，用于提示博主和显示<作者>小标签
    'owner_nick' => '博主', // 博主称呼，主要用于logs文件里的显示（可以保持默认）
    'mail_subject' => '您在[夜街尘]的留言有了新的回应', // 邮件主题/标题
    'mail_template_filename' => 'template.html', // 邮件模板文件
    'mail_logs_filename' => 'logs.txt', // logs文件
    'mail_snapshot_filename' => 'snapshot.html', // 邮件快照文件
    'site_url' => 'https ://xx.cn', // 站点URL，用于邮件内的超链接
    'site_title' => '夜街尘', // 站点名
    'site_description' => '回望间，便是光年之遥。眨眼间，已是千年之外', // 站点描述
    'smtp_host'     => 'smtpdm. aliyun. com', // smtp服务器地址
    'smtp_username' => 'xx@ xx.cn', // smtp用户名
    'smtp_password' => 'F65FG8 g87 fgg3', // smtp密码
    'smtp_secure'   => 'ssl', // 加密方式，'' or 'ssl' or 'tls' 
    'smtp_port'     => 465, // smtp端口
    'smtp_from_mail' => 'xx@ xx.cn', // 发件邮箱（通常需要和smtp_username一致）
    'smtp_from_name' => '夜街尘1', // 发件人名字（对方邮箱里的'发件人'名字）
    'smtp_test_mode' => false, // 测试模式不会真的发送邮件（日志还是有的）
    'smtp_snapshot' => true, // 保存最后的邮件内容快照（仅供调试）
    'cookie_refresh_period' => 600, // 浏览统计（单位秒），超过这个时间以后会被视为新的访客
    'smilies_dir' => 'smilie_sets', // 表情包文件夹, 用于扫描
    'smilies_http_header' => 'http://127.0.0.1:4454/', // 表情包文件夹, 用于访问（记得放开安全策略）
    'smilies_setting_file' => 'smilies_settings.json', // 表情包设置文件
    'gravatar_default' => '', // 默认头像，可选值: <留空>, 404, mp, identicon, monsterid, wavatar, retro, robohash, blank
    'gravatar_nocache' => false, // 是否不缓存头像，这个一般情况下建议设置为false
];

/*
gravatar_default详细说明:
具体参考 https://en.gravatar.com/site/implement/images/ 或者 https://valine.js.org/avatar.html

直接留空: 返回Gravatar默认的头像
404: 直接返回http404
mp: 神秘人(一个灰白头像)
identicon: 抽象几何图形
monsterid: 小怪物
wavatar: 用不同面孔和背景组合生成的头像
retro: 八位像素复古头像
robohash: 一种具有不同颜色、面部等的机器人
blank: 返回透明png图片

*/

?>