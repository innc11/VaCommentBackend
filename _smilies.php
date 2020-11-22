<?php


/**
 * 获取表情译码表(排序过的/sorted)
 * 
 * @param excludeDisabled 是否排除禁用的表情包内容
 */
function smilieTranslations($config, $excludeDisabled=false)
{
    $urlHeader = $config->smilies_http_header;
    $smSettings = getSmilieSettings($config);
    $smsetsort = $smSettings->sorted;
    $smdisable = $smSettings->disabled;

    $smtrans = [];

    // 获取所有表情包
    $all_smilie_set = getDir($config->smilies_dir)->files;
    foreach($all_smilie_set as $smilie_set) {
        $raw_file = $smilie_set;
        $smilie_set = str_replace('.json', '', $smilie_set); // 去掉 .json
        if (in_array($smilie_set, $smdisable) && $excludeDisabled)
            continue;

        // 获取所有表情文件
        $smilies = json_decode(file_get_contents(realpath($config->smilies_dir.'/'.$raw_file)));
        $smtrans[$smilie_set] = [];
        foreach($smilies as $smilie) {
            $tag = $smilie;
            $smtrans[$smilie_set][$tag] = $urlHeader . $smilie_set . '/' . $smilie;
        }
    }

    $sortfun = function($obj1, $obj2) use ($smsetsort) {
        $p1 = array_search($obj1, $smsetsort);
        $p2 = array_search($obj2, $smsetsort);
        $p1 = $p1===false? -1:$p1;
        $p2 = $p2===false? -1:$p2;

        if ($p1 < $p2) return -1;
        if ($p1 > $p2) return 1;
        if ($p1 == $p2) return 0;
    };

    uksort($smtrans, $sortfun);
    return $smtrans;
}

/**
 * 解析表情图片
 */
function showsmilies($config, $content)
{
    // 表情/url 映射表
    $smtrans = smilieTranslations($config);
    $smiliesTag = [];
    $smiliesImg = [];
    foreach ($smtrans as $smilieSet => $smilies) {
        foreach ($smilies as $tag => $grin) {
            $smiliesTag[] = $tag;
            $smiliesImg[] = '<img class="smilie" src="'.$grin.'" alt="'.basename($grin).'" style="max-width:84px !important;max-height: 84px !important;display:inline-block;"/>';
        }
    }

    array_walk($smiliesTag, function(&$v, $k) {
        $v = ':'.$v.':';
    });
    $content = str_replace($smiliesTag, $smiliesImg, $content);

    return $content;
}

// 获取表情设置（排序/禁用）
function getSmilieSettings($config)
{
    $smilieSetSettingFile = $config->smilies_setting_file;
    $temp = file_exists($smilieSetSettingFile)? json_decode(file_get_contents($smilieSetSettingFile)):[[],[]];
    $sortedSmilieSet = $temp[0];
    $disabledSmilieSet = $temp[1];

    return (object) [
        'sorted' => $sortedSmilieSet,
        'disabled' => $disabledSmilieSet,
    ];
}

// ------------------

function onSmilieSetPanel($request, $response, $service, $app)
{
    $temp = getSmilieSettings($app->config);
    $sortedSmilieSet = $temp->sorted;
    $disabledSmilieSet = $temp->disabled;
    
    ?>
    <!DOCTYPE HTML>
    <html>
        <head>
            <meta charset="utf-8">
            <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/jquery.gridly@1.3.0/javascripts/jquery.gridly.min.js"></script>
            <style><?php echo(file_get_contents(dirname(__FILE__) . '/assets/backend.css')); ?></style>
            <script><?php echo(file_get_contents(dirname(__FILE__) . '/assets/backend.js')); ?></script>
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
                <br/>下方的编辑框内容请不要改动，如果出现异常请删除表情设置文件（通常是smilies_settings.json，具体请参考config.php里的smilies_setting_file）
                <br/>设置完成后点击"提交按钮生效"</p>
            </div>

            <form action="smilie_api" method="post">
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

// 请求表情包或者表情
function onSmiliesRequested($request, $response, $service, $app)
{
    // 返回Json格式
    header('Content-Type:application/json;charset=utf-8');

    $lists = [];
    $imgUrlHeader = $app->config->smilies_http_header;
    
    // 如果不是跨域访问的话应该就是后台访问，后台访问需要显示所有表情包
    $ExcludeDisabled = isset(getAllHttpHeaders()['Origin']);

    if (isset($request->set)) {
        // 获取表情包文件夹下的所有表情
        $all = smilieTranslations($app->config, $ExcludeDisabled);
        $set = $request->set;
        $smilies = isset($all[$set])? array_keys($all[$set]):[];
        $lists = [$imgUrlHeader, $smilies];
    } else {
        // 获取所有表情包
        $sms = [];
        foreach(smilieTranslations($app->config, $ExcludeDisabled) as $k => $v) {
            $sms[] = [$k, array_keys($v)[0]];
        }
        $lists = [$imgUrlHeader, $sms];
    }
    echo(json_encode($lists));
}

function onSmilieAPIRequested($request, $response, $service, $app)
{
    $service->back();
    file_put_contents('smilies_settings.json', json_encode([
        json_decode($request->sorted),
        json_decode($request->disabled)
    ]));
}

// 性能太低故注释
// 处理静态文件
// function onSmilieFileRequested($request, $response, $service, $app)
// {
//     $f = dirname(__FILE__).'/'.$app->config->smilies_dir.'/'.$request->dir.'/'.$request->file;
//     if (file_exists($f)) {
//         $fi = new finfo(FILEINFO_MIME_TYPE);
//         $mime_type = $fi->file($f);
//         header('Content-Type:'.$mime_type);
//         header('Content-Length:'.filesize($f));
//         header('Accept-Ranges: bytes');
//         echo(file_get_contents($f));
//     } else {
//         $response->code(404);
//     }
// }


$router->respond('GET',  '/smilie_api/[:set]?', 'onSmiliesRequested'); // 获取表情包列表或者表情列表
$router->respond('POST', '/smilie_api', 'onSmilieAPIRequested'); // 返回表单数据
$router->respond('GET',  '/smilie_set_panel', 'onSmilieSetPanel'); // 管理页面
// $router->respond('GET',  '/smilie_sets/[:dir]/[:file]', 'onSmilieFileRequested'); // 静态文件处理(性能太低故注释)

?>