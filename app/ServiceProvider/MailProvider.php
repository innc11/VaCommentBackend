<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Model\CommentNotificationModel;
use Mail\MailClient;

class MailProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['mail'] = (object)[
            'send' => fn(...$params) => self::sendMail(...$params),
            'log' => fn(...$params) => self::log(...$params),
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