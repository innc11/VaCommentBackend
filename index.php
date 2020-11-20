<?php
    ini_set('date.timezone','Asia/Shanghai');

    require('utils.php');
    require('config.php');
    require('cors.php');
    require('database.pdo.php');
    require('mail.php');

    header('Content-Type:application/json;charset=utf-8');
    
    $db = new DatabaseTool();

    require('addComment.php');
    require('requestComments.php');
?>