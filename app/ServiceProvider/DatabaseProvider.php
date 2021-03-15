<?php

namespace ServiceProvider;

use Pimple\Container;

class DatabaseProvider extends Base\ServiceProviderBase
{
    public function onRegisterRule(Container &$container)
    {
        $this->registerService('database', function() {
            $statement = <<<EOF
            CREATE TABLE IF NOT EXISTS 'comments' (
                'id'         INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                'parent'     TEXT,
                'key'        TEXT,
                'label'      TEXT,
                'nick'       TEXT,
                'mail'       TEXT,
                'website'    TEXT,
                'content'    TEXT,
                'approved'   BOOL,
                'time'       DATETIME,
                'ip'         TEXT,
                'useragent'  TEXT
            );              
            EOF;

            $statement2 = <<<EOF
            CREATE TABLE IF NOT EXISTS 'views' (
                'id'         INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                'key'        TEXT,
                'label'    TEXT,
                'time'       DATETIME,
                'ip'         TEXT,
                'useragent'  TEXT
            );
            EOF;

            $db = new \Database\PdoSqliteDatabase('sqlite:'.DATABASE_PATH);
            $db->prepare($statement)->execute()->end();
            $db->prepare($statement2)->execute()->end();

            return $db;
        });
    }
}

?>