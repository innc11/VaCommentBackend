<?php
    session_start();
    ini_set('date.timezone','Asia/Shanghai');
    require 'vendor/autoload.php';
    if (file_exists('config.dev.php'))
        require 'config.dev.php';
    else
        require 'config.php';
    require 'utils.php';
    require 'cors.php';
    require 'database.pdo.php';
    
    $router = new \Klein\Klein();

    $router->respond(function($request, $response, $service, $app){
        $app->register('db', function(){
            return new DatabaseTool('sqlite:database.sqlite3');
        });

        $app->register('config', function(){
            global $config;
            return $config;
        });

    });

    require('_addComment.php');
    require('_requestComments.php');
    require('_captcha.php');
    require('_smilies.php');

    $router->onHttpError(function($code, $router, $matched, $methods_matched, $http_exception) {
        $router->response()->body('It seems something wrong! '.$code.'('.$router->request()->uri().')');
    });

    $router->dispatch();
?>