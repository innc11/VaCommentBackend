<?php

define('APP_VER', '1.2.3');

// Root directory
define('ROOT_DIR', dirname(__DIR__));

// App directory
define('APP_DIR',  ROOT_DIR.DIRECTORY_SEPARATOR.'app');

// Assets directory
define('ASSET_DIR',  ROOT_DIR.DIRECTORY_SEPARATOR.'assets');

// Data directory location
defined('DATA_DIR') or define('DATA_DIR', getenv('DATA_DIR') ?: ROOT_DIR.DIRECTORY_SEPARATOR.'data');

// 目录分隔符别名
// defined('DIR_SEP', DIRECTORY_SEPARATOR);

?>