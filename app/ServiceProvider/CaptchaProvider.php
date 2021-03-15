<?php

namespace ServiceProvider;

use Pimple\Container;

class CaptchaProvider extends Base\ServiceProviderBase
{
    public function onRegisterRule(Container &$container)
    {
        self::registerRule('GET', '/captcha', 'captcha');
    }

    public function captcha(array $params)
    {
        $captcha = new \Captcha\CaptchaGenerator(realpath(ASSET_DIR.DIRECTORY_SEPARATOR.'captcha'.DIRECTORY_SEPARATOR.'VarelaRound-Regular.ttf'));
        $captcha->generate();
        $captcha->outputHeader();
        $captcha->outputBody();
        $code = $captcha->getCode();

        \Utils\Utils::setCookie('captcha', md5(strtolower($code)));
    }
}

?>