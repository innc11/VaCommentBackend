<?php

namespace Analysis;

use Model\VisitModel;

class Visit
{
    public static function newVisitor($db, VisitModel $visit)
    {
        $sql = "INSERT INTO 'views' (key, comment, time, ip, useragent)".
        "VALUES (:key, :comment, :time, :ip, :useragent)";

        $db->prepare($sql)->execute([
            'key' => $visit->key,
            'comment' => $visit->pageComment,
            'time' => time(),
            'ip' => $visit->ip,
            'useragent' => $visit->useragent
        ])->end();
    }
}

?>