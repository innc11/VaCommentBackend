<?php

namespace ServiceProvider;

use Pimple\Container;
use Database\PdoSqliteDatabase;

class DatabaseProvider extends ServiceProviderBase
{
    public function onRegisterRule(Container $container)
    {
        $container['database'] = function() { 
            return new PdoSqliteDatabase('sqlite:'.DATABASE_PATH);
        };
    }
}

?>