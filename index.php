<?php

try {
    require __DIR__.'/app/Common.php';
    $container['router']->dispatch();
} catch (Exception $e) {
    echo
<<<EOF
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Internal Error</title>
        <style>
            html {
                padding: 50px 10px;
                font-size: 16px;
                line-height: 1.4;
                color: #666;
                background: #F6F6F3;
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;
            }

            html,
            input { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; }
            body {
                _width: 500px;
                padding: 30px 20px;
                margin: 0 auto;
                background: #FFF;
            }
            ul {
                padding: 0 0 0 40px;
            }
            .container {
                _width: 380px;
                margin: 0 auto;
            }
        </style>
    </head>
    <body>
        <div class="container">
            Internal Error: {$e->getMessage()}
            <br/><br/>
            <pre style="font-family: &quot;Microsoft YaHei&quot;;font-size: 12px;">{$e}</pre>
        </div>
    </body>
</html>
EOF;
}

?>