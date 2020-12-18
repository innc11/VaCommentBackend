<?php

namespace Exception;

use \Exception;

class FileNotFoundException extends Exception
{
    public function __construct($path)
    {
        $message = '找不到文件: <b>'.$path.'</b>';
        $code = 0;
        parent::__construct($message, $code);
    }
}


?>