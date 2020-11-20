<?php
class Router
{
    public $routes = [];

    public function register(string $url, string $callback)
    {
        if (seekRoute($url)!=-1)
            $routes[] = [ $url, $callback ];
    }

    public function seekRoute(string $url)
    {
        $index = 0;
        foreach ($this->routes as $route) {
            if ($route[0] == $url)
                return $index;
            $index += 1;
        }
        return -1;
    }

    public function unregister($url)
    {
        $index = seekRoute($url);
        if ($index!=-1)
            unset($routes[$index]);
    }

    public function route($url)
    {
        $index = seekRoute($url);
        if ($index!=-1)
            return $routes[$index][1];
        return NULL;
    }

}


?>