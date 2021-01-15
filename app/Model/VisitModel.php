<?php

namespace Model;

class VisitModel extends ModelBase
{
    public string $key;
    public string $pageComment;
    public string $ip;
    public string $useragent;

    public function __construct(string $key, string $pageComment, string $ip, string $useragent)
    {
        $this->key = $key;
        $this->pageComment = $pageComment;
        $this->ip = $ip;
        $this->useragent = $useragent;
    }
    
    public static function FromArray(array $obj)
    {
        $key = $obj['subject'];
        $pageComment = $obj['recipients'];
        $ip = $obj['purpose'];
        $useragent = $obj['testMode'];

        return new VisitModel($key, $pageComment, $ip, $useragent);
    }
}

?>