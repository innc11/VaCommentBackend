<?php

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/Constants.php';
require __DIR__.'/AutoUsing/AutoUsing.php';
require __DIR__.'/CheckSetup.php';

// 加载配置文件
$config_file = ROOT_DIR.DIRECTORY_SEPARATOR.'config.php';

if(!file_exists($config_file))
    throw new Exception\ConfigFileNotFoundException($config_file);

require $config_file;

ini_set('date.timezone',TIMEZONE);
ini_set('default_charset', 'UTF-8');

Cors\CorsHandle::call();

if(!file_exists(DATA_DIR))
    mkdir(DATA_DIR);

Log\Log::init(LOG_FILE);

//-----------------------------------------------------------------

$container = new Pimple\Container();

$router = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector &$r) use(&$container) {
    (new ServiceProvider\DatabaseProvider())->register($r, $container);
    (new ServiceProvider\AnalysisProvider())->register($r, $container);
    (new ServiceProvider\MailProvider())->register($r, $container);
    (new ServiceProvider\CommentAPIProvider())->register($r, $container);
    (new ServiceProvider\SmiliesAPIProvider())->register($r, $container);
    (new ServiceProvider\CaptchaProvider())->register($r, $container);
    (new ServiceProvider\CommentManageProvider())->register($r, $container);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $router->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) 
{
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo 'Something went wrong 404 ('.$uri.')'.$_SERVER['REQUEST_METHOD'];
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo 'Something went wrong 405 ('.$uri.')'.$_SERVER['REQUEST_METHOD'];
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // ... call $handler with $vars
        // echo var_export($vars);
        // echo get_class($vars);
        $handler($vars);
        break;
}


?>