<?php
    session_start();

    header('Content-Type:application/json;charset=utf-8');
    header('Access-Control-Allow-Origin: http://127.0.0.1:9981');
    header("Access-Control-Allow-Methods: GET, POST");
    header("Access-Control-Allow-Credentials: true");

    require('config.php');
    require('database.php');
    require('utils.php');
    require('mail.php');
    
    require('addComment.php');
    require('requestComments.php');

?>