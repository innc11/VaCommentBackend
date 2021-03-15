<?php

namespace Utils;

class Utils
{
    public static function getAvatarByMail($mail)
    {
        $paragmeters = [
            GRAVATAR_NOCACHE? 'f=y':'',
            'd='.GRAVATAR_DEFAULT,
        ];
        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($mail))).'?'.implode('&', $paragmeters);
    }

    // https://blog.csdn.net/ahaotata/article/details/84999015
    public static function httpHeaders()
    {
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    public static function setCookie($key, $value, $_expires=0)
    {
        // _expires: 绝对时间
        // SameSite => None || Lax  || Strict
        
        $secure = isset($_SERVER["HTTPS"]);
        $timeout = date('D, d-M-Y H:i:s', $_expires).' GMT';

        $expires = $_expires>0? "expires=$timeout;":'';
        $sameSite = $secure? 'SameSite=None; Secure;':'';
        $path = 'Path=/;';

        header("Set-Cookie: $key=$value; $expires $sameSite $path");
    }

    public static function checkMissingParameters($array, $fields)
    {
        $missings = [];

        foreach ($fields as $f) {
            if (trim($f)) {
                if (!isset($array[$f]) || !$array[$f])
                    $missings[] = $f;
            }
        }

        return $missings;
    }

    public static function checkMissingParameters2($array, $fields, $strict=false)
    {
        $missings = [];

        foreach ($fields as $f) {
            if (trim($f)) {
                if (!isset($array[$f]) || ($strict && !$array[$f]))
                    $missings[] = $f;
            }
        }

        if(count($missings) > 0)
            throw new \Exception\MissingNecessaryParametersException($missings);
    }

    public static function getDir($path = '')
    {
        if(!file_exists($path))
            throw new \Exception\DirectoryNotFoundException($path);

        $dirs =  [];
        $files = [];
        $f = realpath($path);

        if (file_exists($f)) {
            foreach (scandir($f) as $file) {
                if($file=='.' or $file=='..')
                    continue;

                if (strpos($file, '.')===false)
                    $dirs[] = $file;
                else
                    $files[] = $file;
            }
        }

        return (object) [
            'dirs' => $dirs,
            'files' => $files
        ];
    }
}


?>