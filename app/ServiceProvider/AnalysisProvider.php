<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Analysis\Visit;

class AnalysisProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $func_newVisitor = function(...$params) {
            Visit::newVisitor(...$params);
        };
        
        $container['analysis'] = [
            'visit' => $func_newVisitor
        ];
    }
}

?>