<?php

namespace Analysis;

use Model\VisitModel;

class Visit
{
    public static function newVisitor($db, VisitModel $visit)
    {
        $sql = "INSERT INTO 'views' (url, title, time, ip, useragent)".
        "VALUES (:url, :title, :time, :ip, :useragent)";

        $db->prepare($sql)->execute([
            'url' => $visit->url,
            'title' => $visit->pageTitle,
            'time' => time(),
            'ip' => $visit->ip,
            'useragent' => $visit->useragent
        ])->end();
    }
}

?>