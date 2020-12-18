<?php

namespace Smilie;

class SmilieSystem
{
    /**
     * 获取排序过的表情译码表
     * 
     * @param excludeDisabled 是否排除禁用的表情包内容
     */
    public static function smilieTranslations(bool $excludeDisabled=false)
    {
        $smSettings = self::getSmilieSettings();
        $smSetsort = $smSettings->sorted;
        $smDisable = $smSettings->disabled;

        $smtrans = [];

        // 获取所有表情包
        $all_smilie_set = \Utils\Utils::getDir(SMILIE_DIR)->files;
        foreach($all_smilie_set as $smilie_set) {
            $raw_file = $smilie_set;
            $smilie_set = str_replace('.json', '', $smilie_set); // 去掉 .json
            if (in_array($smilie_set, $smDisable) && $excludeDisabled)
                continue;

            // 获取所有表情文件
            $smilies = json_decode(file_get_contents(realpath(SMILIE_DIR.DIRECTORY_SEPARATOR.$raw_file)));
            $smtrans[$smilie_set] = [];
            foreach($smilies as $smilie) {
                $tag = $smilie;
                $smtrans[$smilie_set][$tag] = SMILIE_URL.$smilie_set.'/'.$smilie;
            }
        }

        $sortfun = function($obj1, $obj2) use ($smSetsort) {
            $p1 = array_search($obj1, $smSetsort);
            $p2 = array_search($obj2, $smSetsort);
            $p1 = $p1===false? -1:$p1;
            $p2 = $p2===false? -1:$p2;

            if ($p1 < $p2) return -1;
            if ($p1 > $p2) return 1;
            if ($p1 == $p2) return 0;
        };

        uksort($smtrans, $sortfun);
        return $smtrans;
    }

    public static function getSmilieSettings()
    {
        $data = file_exists(SMILIE_CONFIG_FILE)? json_decode(file_get_contents(SMILIE_CONFIG_FILE)):[[],[]];
    
        return (object) [
            'sorted' => $data[0],
            'disabled' => $data[1],
        ];
    }

    // 解析表情图片
    public static function showSmilies($content)
    {
        // (表情 -> url) 映射表
        $smtrans = self::smilieTranslations();
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

        return str_replace($smiliesTag, $smiliesImg, $content);
    }
}

?>