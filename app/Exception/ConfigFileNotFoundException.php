<?php

namespace Exception;

use \Exception;

class ConfigFileNotFoundException extends BaseExceptionWithMessages
{
    public function __construct($path)
    {
        $message = '找不到配置文件: <b>'.$path.'</b>';
        $code = 0;
        parent::__construct($message, $code);
    }
}


?>