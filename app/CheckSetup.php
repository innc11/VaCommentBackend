<?php

// PHP 7.2.0 minimum
if (version_compare(PHP_VERSION, '7.2.0', '<')) {
    throw new Exception('This software requires PHP 7.2.0 minimum');
}

// Check data folder if sqlite
if (!is_writable(DATA_APP)) {
    throw new Exception('The directory "'.DATA_APP.'" must be writeable by your web server user');
}

// Check PDO extensions
if (!extension_loaded('pdo_sqlite')) {
    throw new Exception('PHP extension required: "pdo_sqlite"');
}

// Check other extensions
foreach (array('gd', 'json') as $ext) {
    if (! extension_loaded($ext)) {
        throw new Exception('This PHP extension is required: "'.$ext.'"');
    }
}

?>