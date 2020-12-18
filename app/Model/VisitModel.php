<?php

namespace Model;

class VisitModel extends ModelBase
{
    public string $url;
    public string $pageTitle;
    public string $ip;
    public string $useragent;

    public function __construct(string $url, string $pageTitle, string $ip, string $useragent)
    {
        $this->url = $url;
        $this->pageTitle = $pageTitle;
        $this->ip = $ip;
        $this->useragent = $useragent;
    }
    
    public static function FromArray(array $obj)
    {
        $url = $obj['subject'];
        $pageTitle = $obj['recipients'];
        $ip = $obj['purpose'];
        $useragent = $obj['testMode'];

        return new VisitModel($url, $pageTitle, $ip, $useragent);
    }
}

?>