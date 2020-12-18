<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Captcha\CaptchaGenerator;

class CaptchaProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['router']->respond('GET', '/captcha', fn(...$params) => self::onCaptchaRequest(...$params),);
    }

    public static function onCaptchaRequest($request, $response, $service, $app)
    {
        $captcha = new CaptchaGenerator(realpath(ASSET_DIR.DIRECTORY_SEPARATOR.'captcha'.DIRECTORY_SEPARATOR.'arial.ttf'));
        $captcha->generate();
        $code = $captcha->getCode();

        // $_SESSION["captcha"] = $captcha;
        \Utils\Utils::setCookie('captcha', md5(strtolower($code)));
    }
}

?>