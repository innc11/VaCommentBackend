<?php

function getSql($file)
{
    if (!file_exists($file))
    {
        throw new Exception("文件不存在: ".$file);
    }
    $script = file_get_contents($file);
    $script = str_replace('%charset%', 'utf8', $script);
    $script = explode(';', $script);

    $statements = [];

    foreach ($script as $statement)
    {
        $statement = trim($statement);

        if ($statement)
            array_push($statements, $statement);
    }

    return implode(';', $statements) . ';';
}

$pdo = new PDO('sqlite:comments.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec(getSql('create.sql'));


?>