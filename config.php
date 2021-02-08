<?php

// ###########################################################################################
// #       ____    ____         __           _____       _____          _______              #
// #     |_   \  /   _|       /  \         |_   _|     |_   _|        /  ___  |              #
// #      |   \/   |        / /\ \          | |         | |         |  (__ \_|               #
// #     | |\  /| |       / ____ \         | |         | |   _      '.___`-.                 #
// #   _| |_\/_| |_    _/ /    \ \_      _| |_       _| |__/ |    |`\____) |                 #
// # |_____||_____|  |____|  |____|    |_____|     |________|    |_______.'                  #
// #                                                                                         #
// ###########################################################################################

// 站点标题
define('MAIL_SITE_TITLE', '');

// 站点副标题
define('MAIL_SITE_SUBTITLE', '');

// 站点域名，用于邮件内的超链接
define('MAIL_SITE_URL', '');

// 邮件主题/标题
define('MAIL_SUBJECT', '');

// 博主邮箱，用于提示是博主和显示<作者>小标签
define('MAIL_OWNER_MAIL', '');

// 博主称呼，主要用于logs和邮件通知
define('MAIL_OWNER_NAME', '作者');

// 邮件日志文件
define('MAIL_LOG_FILE', DATA_DIR.DIRECTORY_SEPARATOR.'logs.log');

// 邮件模板文件
define('MAIL_TEMPLATE_FILE', DATA_DIR.DIRECTORY_SEPARATOR.'template.html');

// 测试模式，处于不会真的发送邮件（但日志还是有的）
define('MAIL_TEST_MODE', true);

// 保存最后的邮件内容快照（For debuging）
define('MAIL_SNAPSHOT_ENABLE', true);

// 邮件快照文件位置
define('MAIL_SNAPSHOT_FILE', DATA_DIR.DIRECTORY_SEPARATOR.'snapshot.html');

// ###########################################################################################
// #           _______     ____    ____     _________      ______                           #
// #         /  ___  |   |_   \  /   _|    |  _   _  |    |_   __ \                          #
// #       |  (__ \_|     |   \/   |      |_/ | | \_|      | |__) |                          #
// #       '.___`-.       | |\  /| |          | |          |  ___/                            #
// #     |`\____) |     _| |_\/_| |_        _| |_        _| |_                                #
// #    |_______.'    |_____||_____|      |_____|      |_____|                               #
// #                                                                                         #
// ###########################################################################################

// SMTP 服务器地址
define('SMTP_HOST', '');

// SMTP 用户名
define('SMTP_USERNAME', '');

// SMTP 密码
define('SMTP_PASSWORD', '');

// SMTP 加密方式, 'ssl' or 'tls' or ''
define('SMTP_SECURE', 'ssl');

// SMTP 端口
define('SMTP_PORT', 465);

// SMTP 发件邮箱(通常需要和SMTP_USERNAME一致)
define('SMTP_FROM_MAIL', '');

// SMTP 发件名（对方邮箱里的'发件人'名字）
define('SMTP_FROM_NAME', '');

// ###########################################################################################
// #          _______     ____    ____      _____      _____         _____      _________    #
// #        /  ___  |   |_   \  /   _|    |_   _|    |_   _|       |_   _|    |_   ___  |    #
// #      |  (__ \_|     |   \/   |        | |        | |           | |        | |_  \_|     #
// #      '.___`-.       | |\  /| |        | |        | |   _       | |        |  _|  _      #
// #    |`\____) |     _| |_\/_| |_      _| |_      _| |__/ |     _| |_      _| |___/ |      #
// #   |_______.'    |_____||_____|    |_____|    |________|    |_____|    |_________|       #
// #                                                                                         #
// ###########################################################################################

// Smilie directory
define('SMILIE_DIR', DATA_DIR.DIRECTORY_SEPARATOR.'smilies');

// 表情包文件夹, 用于实际访问（如果使用云计算，记得放开安全策略），需要加上 /
define('SMILIE_URL', '');

// 表情包设置文件
define('SMILIE_CONFIG_FILE', DATA_DIR.DIRECTORY_SEPARATOR.'smilies_settings.json');

// ###########################################################################################
// #        ______             __            _______       _____          ______             #
// #      |_   _ \           /  \          /  ___  |     |_   _|       .' ___  |             #
// #       | |_) |         / /\ \        |  (__ \_|       | |        / .'   \_|              #
// #      |  __'.        / ____ \        '.___`-.         | |       | |                       #
// #    _| |__) |     _/ /    \ \_     |`\____) |       _| |_      \ `.___.'\                 #
// #  |_______/     |____|  |____|    |_______.'      |_____|      `._____.'                  #
// #                                                                                         #
// ###########################################################################################

// 是否动态响应所有的CORS请求（调试好后建议关闭并开启CORS_ALLOW_HOST）
define('CORS_ALLOW_ALL', true);

// CORS白名单站点(CORS_ALLOW_ALL为false时有效)
define('CORS_ALLOW_HOST', 'http://127.0.0.1:4001');

// Sqlite数据库文件的路径
define('DATABASE_PATH', DATA_DIR.DIRECTORY_SEPARATOR.'database.sqlite3');

// 时区设置(参考 https://www.php.net/manual/zh/timezones.php )
define('TIMEZONE', 'Asia/Shanghai');

// 每页显示多少评论
define('PAGE_CAPACITY', 5);

// 开发模式下不检查验证码
define('DEVELOPMENT_MODE', true);

// 浏览统计（单位秒），超过这个时间以后会被视为新的访客
define('PERIOD_AS_NEW_VISITOR', 600);

// 后台用户名和密码
define('ADMIN_USER', 'aaa');
define('ADMIN_PASSWORD', 'aaa');

/* 
默认头像，可选值: <留空>, 404, mp, identicon, monsterid, wavatar, retro, robohash, blank
gravatar_default详细说明具体参考 https://en.gravatar.com/site/implement/images/ 或者 https://valine.js.org/avatar.html

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
define('GRAVATAR_DEFAULT', '');

// 是否不缓存头像，这个一般情况下建议设置为false
define('GRAVATAR_NOCACHE', false);

?>