<?php

namespace ServiceProvider;

use Pimple\Container;

class AnalysisProvider extends Base\ServiceProviderBase
{
    public function onRegisterRule(Container &$container)
    {
        $func_newVisitor = function(...$params) {
            \Analysis\Visit::newVisitor(...$params);
        };
        
        $container['analysis'] = [
            'visit' => $func_newVisitor
        ];
    }
}

?>