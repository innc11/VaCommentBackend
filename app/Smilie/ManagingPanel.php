<?php

namespace Smilie;

class ManagingPanel
{
    public static function onPanelRender(string $submitingAddress)
    {
        $temp = SmilieSystem::getSmilieSettings();
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
            <body>
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

    public static function onPanelSubmit(string $sorted, string $disabled)
    {
        $service->back();

        file_put_contents(SMILIE_CONFIG_FILE, json_encode([
            json_decode($sorted),
            json_decode($disabled)
        ]));
    }
}

?>