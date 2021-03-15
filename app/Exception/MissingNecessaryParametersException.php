<?php

namespace Exception;

use \Exception;

class MissingNecessaryParametersException extends BaseExceptionWithMessages
{
    public function __construct(array $missings)
    {
        $message = 'Missing Necessary Parameters Exception: "'.json_encode($missings).'"';
        $code = 403;
        parent::__construct($message, $code);
    }
}

?>