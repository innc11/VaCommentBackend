<?php

class DatabaseTool
{
    public $sqlite;

    public $_statement;
    public $_result;

    public function __construct(string $path='comments.db')
    {
        $this->sqlite = new SQLite3($path);
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

        $this->sqlite->exec(implode(';', $statements));
    }

    public function prepare(string $sql)
    {
        $this->_statement = $this->sqlite->prepare($sql);
        return $this;
    }

    public function execute(array $parameter=[])
    {
        foreach ($parameter as $k => $v) 
            $this->_statement->bindValue(':'.$k, $v);
        $this->_result = $this->_statement->execute();
        return $this;
    }

    public function fetchAll()
    {
        $this->execute();

        $fetchResult = [];
        while ($res = $this->_result->fetchArray(SQLITE3_ASSOC))
            $fetchResult[] = $res;

        $this->close();
        return $fetchResult;
    }

    public function close()
    {
        $this->_statement->close();
        return $this;
    }

    public function result()
    {
        return $this->_result;
    }

    
}



?>