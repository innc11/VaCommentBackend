<?php

namespace ServiceProvider\Base;

use \Exception;
use \Pimple\Container;
use \Klein\Klein;

abstract class ServiceProviderBase
{
    public $routeCollector; // instance of FastRoute\RouteCollector
    public $container; // instance of Pimple/Container
    private $routeTable = [];

    public function register(\FastRoute\RouteCollector &$r, Container &$container)
    {
        $this->routeCollector = $r;
        $this->container = $container;

        $this->onRegisterRule($container);

        // echo get_called_class() . PHP_EOL;
        // echo json_encode($this->routeTable, JSON_PRETTY_PRINT);
        // echo PHP_EOL.PHP_EOL;
    }

    protected abstract function onRegisterRule(Container &$container);

    protected function registerRule(string $mothod, string $pattern, string $methodName)
    {
        $id = sha1($mothod.$pattern);
        
        $this->routeCollector->addRoute(strtoupper($mothod), $pattern, function(...$params) use($id) { 
            self::dispatch($id, ...$params);
        });

        $insert = [
            $id => [
                $methodName,
                $mothod,
                $pattern
            ]
        ];
    
        $this->routeTable = array_merge($this->routeTable, $insert);
    }

    protected function getService(string $serviceName)
    {
        // var_export($this->container->keys());
        return $this->container[$serviceName];;
    }

    protected function registerService(string $serviceName, callable $factoryFunc)
    {
        $this->container[$serviceName] = $this->container->factory($factoryFunc);
    }

    private function dispatch(string $id, ...$params)
    {
        $method = $this->routeTable[$id][0];
        
        if(\method_exists($this, $method))
        {
            $this->$method(...$params);
        } else {
            throw new Exception("Method \"$method\" not found in ".get_called_class());
        }
    }
}

?>