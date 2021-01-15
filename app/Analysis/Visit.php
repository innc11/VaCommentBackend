<?php

namespace Analysis;

use Model\VisitModel;

class Visit
{
    public static function newVisitor($db, VisitModel $visit)
    {
        $sql = "INSERT INTO 'views' (key, label, time, ip, useragent)".
        "VALUES (:key, :label, :time, :ip, :useragent)";

        $db->prepare($sql)->execute([
            'key' => $visit->key,
            'label' => $visit->pageLabel,
            'time' => time(),
            'ip' => $visit->ip,
            'useragent' => $visit->useragent
        ])->end();
    }
}

?>