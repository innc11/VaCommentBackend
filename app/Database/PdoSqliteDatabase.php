<?php

namespace Database;

use \PDO;

class PdoSqliteDatabase
{
    public $pdo;

    public $_statement;

    public function __construct(string $source)
    {
        $this->pdo = new PDO($source);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // 没效果,故注释
        // $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        // $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function executeSQLFromFile(string $path, array $search=[], array $replace=[])
    {
        if (!file_exists($path))
            throw new Exception("文件不存在: ".$path);
        $script = file_get_contents($path);
        $script = str_replace('%charset%', 'utf8', $script);
        $script = str_replace($search, $replace, $script);
        $script = explode(';', $script);
    
        $statements = [];
    
        foreach ($script as $statement) {
            $statement = trim($statement);
            if ($statement)
                array_push($statements, $statement);
        }

        $this->pdo->exec(implode(';', $statements));
    }

    public function prepare(string $sql)
    {
        $a = $this->pdo->prepare($sql);
        $this->_statement = $a;
        return $this;
    }

    public function execute(array $parameter=[])
    {
        $this->_statement->execute($parameter);
        return $this;
    }

    public function fetch()
    {
        $this->execute();
        $result = $this->_statement->fetch();
        $this->close();
        return $result;
    }

    public function fetchAll()
    {
        $this->execute();
        $result = $this->_statement->fetchAll();
        $this->close();
        return $result;
    }

    public function end()
    {
        $this->_statement->closeCursor();
    }

    public function close()
    {
        $this->end();
    }

    
}



?>