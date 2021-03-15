<?php

namespace Exception;

use \Exception;

class DirectoryNotFoundException extends BaseExceptionWithMessages
{
    public function __construct($path)
    {
        $message = '找不到目录: <b>'.$path.'</b>';
        $code = 0;
        parent::__construct($message, $code);
    }
}


?>