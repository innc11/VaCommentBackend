<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Cors\CorsHandle;

class RouteProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $router = new \Klein\Klein();
        $container['router'] = $router;

        $router->onHttpError(function($code, $router, $matched, $methods_matched, $http_exception) {
            $router->response()->body('It seems something wrong! '.$code.'('.$router->request()->uri().')');
        });

        $router->respond(function($request, $response, $service, $app) use($container) {
            $app->register('container', fn() => $container);
        });

        // 处理CORS请求
        CorsHandle::call();
    }
}

?>