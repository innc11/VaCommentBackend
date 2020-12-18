<?php

$VC_AUTOLOAD_PATHS = [
    '',
    // 'Cors',
    // 'Exception'
];

array_walk($VC_AUTOLOAD_PATHS, function(&$value, $key) {
    $value = strlen($value) > 0? APP_DIR.DIRECTORY_SEPARATOR.$value:APP_DIR;
});

define('AUTOLOAD_SEARCH_PATHS', $VC_AUTOLOAD_PATHS);


foreach (AUTOLOAD_SEARCH_PATHS as $path) {
    spl_autoload_register(function ($class_name) use ($path) {
        // 移除顶级命名空间
        // $class_name = substr($class_name, strpos($class_name, '\\') + 1);

        $file = $path.DIRECTORY_SEPARATOR.$class_name.'.php';

        if(file_exists($file))
            require_once $file;
    });
}

?>