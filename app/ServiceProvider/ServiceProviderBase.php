<?php

namespace ServiceProvider;

use \method_exists;
use \Exception;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

abstract class ServiceProviderBase implements ServiceProviderInterface
{
    public $container; // Container

    public function register(Container $container)
    {
        $this->container = $container;
        $container[get_called_class()] = [];
        $this->onRegisterRule($container);

        // echo get_called_class() . PHP_EOL;
        // echo json_encode($this->container[get_called_class()], JSON_PRETTY_PRINT);

        // echo PHP_EOL.PHP_EOL;
    }

    public function registerRule(string $mothod, string $pattern, string $methodName)
    {
        $id = sha1($mothod.$pattern);

        $this->container['router']->respond($mothod, $pattern, function(...$params) use($id) { 
            self::dispatch($id, ...$params);
        });

        $insert = [
            $id => [
                $methodName,
                $mothod,
                $pattern
            ]
        ];
    
        $this->container[get_called_class()] = array_merge($this->container[get_called_class()], $insert);
    }

    public abstract function onRegisterRule(Container $container);

    public function dispatch(string $id, $request, $response, $service, $app)
    {
        $container = $app->container;
        $className = get_called_class();

        $method = $container[$className][$id][0];
        
        if(method_exists($this, $method))
        {
            $this->$method($request, $response, $service, $app);
        } else {
            throw new Exception("Method not found \"$method\" in $className");
        }
    }
}

?>