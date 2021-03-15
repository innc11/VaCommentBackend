<?php

namespace Exception;

use \Exception;

class BaseExceptionWithMessages extends Exception
{
    public $detailForLogging = true;
	public $extraMessage = '';

    public function __construct(...$params)
    {
        parent::__construct(...$params);

        if($this->extraMessage == '')
		    $this->extraMessage = $this->message;
        // $this->extraMessage = get_called_class().': '.$this->message;
    }
}


?>