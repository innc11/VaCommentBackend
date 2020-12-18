<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Database\PdoSqliteDatabase;

class DatabaseProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['database'] = fn() => new PdoSqliteDatabase('sqlite:'.DATABASE_PATH);
    }
}

?>