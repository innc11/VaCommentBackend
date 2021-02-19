<?php

namespace ServiceProvider;

use Pimple\Container;
use Captcha\CaptchaGenerator;

class CaptchaProvider extends ServiceProviderBase
{
    public function onRegisterRule(Container $container)
    {
        self::registerRule('GET', '/captcha', 'captcha');
    }

    public function captcha($request, $response, $service, $app)
    {
        $captcha = new CaptchaGenerator(realpath(ASSET_DIR.DIRECTORY_SEPARATOR.'captcha'.DIRECTORY_SEPARATOR.'arial.ttf'));
        $captcha->generate();
        $code = $captcha->getCode();

        $_SESSION["captcha"] = $captcha;
        \Utils\Utils::setCookie('captcha', md5(strtolower($code)));
    }
}

?>