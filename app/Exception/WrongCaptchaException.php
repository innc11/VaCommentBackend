<?php

namespace Exception;

use \Exception;

class WrongCaptchaException extends BaseExceptionWithMessages
{
    public function __construct(string $reason)
    {
        $message = $reason;
        $code = 403;
        parent::__construct($message, $code);
    }
}

?>