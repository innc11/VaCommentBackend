<?php

try {
    require __DIR__.'/app/Common.php';
    $container['router']->dispatch();
} catch (Exception $e) {
    echo 'vInternal Error: '.$e;
}

?>