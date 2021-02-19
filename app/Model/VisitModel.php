<?php

namespace Model;

class VisitModel extends ModelBase
{
    public $key; // string
    public $pageLabel; // string
    public $ip; // string
    public $useragent; // string

    public function __construct(string $key, string $pageLabel, string $ip, string $useragent)
    {
        $this->key = $key;
        $this->pageLabel = $pageLabel;
        $this->ip = $ip;
        $this->useragent = $useragent;
    }
    
    public static function FromArray(array $obj)
    {
        // $key = $obj['subject'];
        // $pageLabel = $obj['recipients'];
        // $ip = $obj['purpose'];
        // $useragent = $obj['testMode'];

        // return new VisitModel($key, $pageLabel, $ip, $useragent);
        return NULL;
    }
}

?>