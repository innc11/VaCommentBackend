<?php

namespace ServiceProvider;

use Pimple\Container;
use Panel\SmileSetManagingPanel;
use Smilie\SmilieSystem;

class SmiliesAPIProvider extends Base\ServiceProviderBase
{
    public function onRegisterRule(Container &$container)
    {
        self::registerRule('GET',  '/smilie', 'onPanelRender');
        self::registerRule('POST', '/smilie', 'onPanelSubmit');
        self::registerRule('GET',  '/smilie_api[/{set:.*}]', 'onSmiliesAPIRequest');
    }

    public function onPanelRender(array $params)
    {
        $submitingAddress = '/smilie';

        $temp = \Smilie\SmilieSystem::getSmilieSettings();
        $sortedSmilieSet = $temp->sorted;
        $disabledSmilieSet = $temp->disabled;
        
        ?>
        <!DOCTYPE HTML>
        <html>
            <head>
                <meta charset="utf-8">
                <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/jquery.gridly@1.3.0/javascripts/jquery.gridly.min.js"></script>
                <style><?php echo(file_get_contents(ASSET_DIR.DIRECTORY_SEPARATOR.'smilie_panel'.DIRECTORY_SEPARATOR.'backend.css')); ?></style>
                <script><?php echo(file_get_contents(ASSET_DIR.DIRECTORY_SEPARATOR.'smilie_panel'.DIRECTORY_SEPARATOR.'backend.js')); ?></script>
                <script>
                    function resetAll(event) {
                        document.querySelector('#smilies-sorted').value = '[]'
                        document.querySelector('#smilies-disabled').value = '[]'
                        alert('已恢复默认状态，点击提交生效，或者刷新撤销重置')
                    }
                </script>
            </head>
            <body style="width: max-content; margin: 30px auto;">
                <div class="all-smilie-set-wrap">
                    <div class="all-smilie-set gridly">
                        <div>空空如也</div>
                    </div>
                </div>
                <div style="color:#999;font-size:.92857em;">
                    <p>拖动调整表情包的显示顺序，点击表情包图片启用/禁用表情包，禁用后仅不显示，不影响解析
                    <br/>下方的编辑框内容请不要改动，如果出现异常无法显示表情等，请点击下方'重置按钮'然后点击'提交'即可
                    <br/>排序或者启用禁用后需要点击"提交"按钮才会生效</p>
                </div>
    
                <form action="<?php echo($submitingAddress); ?>" method="post">
                    <button type="button" onclick="resetAll(event)">重置</button>
                    <label>表情排序</label>
                    <input type="text" id="smilies-sorted" name="sorted" value="<?php echo(htmlspecialchars(json_encode($sortedSmilieSet))); ?>">
                    <label>禁用表情</label>
                    <input type="text" id="smilies-disabled" name="disabled" value="<?php echo(htmlspecialchars(json_encode($disabledSmilieSet))); ?>">
                    <input type="submit" value="提交">
                </form>
            </body>
        </html>
    
        <?php
    }

    public function onPanelSubmit(array $params)
    {
        $sorted = isset($_POST['sorted'])? $_POST['sorted']:'';
        $disabled = isset($_POST['disabled'])? $_POST['disabled']:'';

        if(empty($sorted) || empty($disabled))
            http_response_code(403);

        file_put_contents(SMILIE_CONFIG_FILE, json_encode([
            json_decode($sorted),
            json_decode($disabled)
        ]));

        echo "<script>history.go(-1);</script>";  
    }

    public function onSmiliesAPIRequest(array $params)
    {
        $headers = \Utils\Utils::httpHeaders();
        $ExcludeDisabled = isset($headers['Origin']); // 如果不是跨域访问的话应该就是后台访问，后台访问需要显示所有表情包
        $smilieTranslations = SmilieSystem::smilieTranslations($ExcludeDisabled);
        $lists = [];

        if (isset($params['set'])) {
            // 获取表情包文件夹下的所有表情
            $all = $smilieTranslations;
            $set = $params['set'];
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

        header('Content-Type:application/json;charset=utf-8');
        echo(json_encode($lists));
    }

}

?>