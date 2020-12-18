<?php

namespace ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Smilie\ManagingPanel;
use Smilie\SmilieSystem;

class SmiliesAPIProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        // 请求面板时
        $container['router']->respond('GET', '/smilie_set_panel', fn(...$params) => self::onPanelRender(...$params),);

        // 面板返回时
        $container['router']->respond('POST', '/smilie_api', fn(...$params) => self::onPanelSubmit(...$params),);

        // 调用表情API时
        $container['router']->respond('GET', '/smilie_api/[:set]?', fn(...$params) => self::onSmiliesAPIRequest(...$params),);
    }

    public static function onPanelRender($request, $response, $service, $app)
    {
        ManagingPanel::onPanelRender('/smilie_api');
    }

    public static function onPanelSubmit($request, $response, $service, $app)
    {
        $sorted = $request->headers()->get('sorted', '');
        $disabled = $request->headers()->get('disabled', '');

        if(empty($sorted) || empty($disabled))
            $response->code(403);

        ManagingPanel::onPanelSubmit($isOriginal, $smilieSet);
    }

    public static function onSmiliesAPIRequest($request, $response, $service, $app)
    {
        // 返回Json格式
        header('Content-Type:application/json;charset=utf-8');

        $ExcludeDisabled = $request->headers()->exists('Origin'); // 如果不是跨域访问的话应该就是后台访问，后台访问需要显示所有表情包
        $smilieTranslations = SmilieSystem::smilieTranslations($ExcludeDisabled);
        $lists = [];

        if (isset($request->set)) {
            // 获取表情包文件夹下的所有表情
            $all = $smilieTranslations;
            $set = $request->set;
            $smilies = isset($all[$set])? array_keys($all[$set]):[];
            $lists = [SMILIE_URL, $smilies];
        } else {
            // 获取所有表情包
            $sms = [];
            foreach($smilieTranslations as $k => $v) {
                $sms[] = [$k, array_keys($v)[0]];
            }
            $lists = [SMILIE_URL, $sms];
        }

        echo(json_encode($lists));
    }

}

?>