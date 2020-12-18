<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/Constants.php';
require __DIR__.'/AutoUsing/AutoUsing.php';

// 加载配置文件
$config_file_dev = ROOT_DIR.DIRECTORY_SEPARATOR.'config.dev.php';
$config_file =     ROOT_DIR.DIRECTORY_SEPARATOR.'config.php';

if (file_exists($config_file_dev)) {
    require $config_file_dev;
} else if (file_exists($config_file)) {
    require $config_file;
} else {
    throw new Exception\ConfigFileNotFoundException($config_file);
}

ini_set('date.timezone',TIMEZONE);

$container = new Pimple\Container();
$container->register(new ServiceProvider\RouteProvider());
$container->register(new ServiceProvider\DatabaseProvider());
$container->register(new ServiceProvider\AnalysisProvider());
$container->register(new ServiceProvider\MailProvider());
$container->register(new ServiceProvider\CommentAPIProvider());
$container->register(new ServiceProvider\SmiliesAPIProvider());
$container->register(new ServiceProvider\CaptchaProvider());



?>