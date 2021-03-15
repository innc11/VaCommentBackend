<?php

try {
    require __DIR__.'/app/Common.php';
} catch (Exception $e) {
    \Exception\Handler\ExceptionHandler::print($e);
}

?>