<?php

namespace ServiceProvider;

use Pimple\Container;
use Model\CommentNotificationModel;
use Mail\MailClient;

class MailProvider extends ServiceProviderBase
{
    public function onRegisterRule(Container $container)
    {
        $container['mail'] = (object)[
            'send' => function(...$params){ self::sendMail(...$params); },
            'log' => function(...$params){ self::log(...$params); },
        ];
    }

    public static function sendMail(CommentNotificationModel $notification)
    {
        MailClient::sendMail($notification);
    }

    public static function log(string $text, string $linebreak=PHP_EOL)
    {
        MailClient::log($text, $linebreak);
    }
}

?>