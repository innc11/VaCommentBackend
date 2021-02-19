<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Cors\CorsHandle;

class RouteProvider extends ServiceProviderBase
{
    public function onRegisterRule(Container $container)
    {
        $router = new \Klein\Klein();
        $container['router'] = $router;

        $router->onHttpError(function($code, $router, $matched, $methods_matched, $http_exception) {
            $router->response()->body('It seems something goes wrong! '.$code.'('.$router->request()->uri().')');
        });

        $router->respond(function($request, $response, $service, $app) use($container) {
            $app->register('container', function() use($container) {
                return $container;
            });
        });

        // 处理CORS请求
        CorsHandle::call();
    }
}

?>