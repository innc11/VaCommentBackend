<?php

session_start();

// 画布
$img = imagecreatetruecolor(100, 30);

// 背景颜色
$bgcolor = imagecolorallocate($img, 250, 250, 250);

// 将背景颜色填充进去
imagefill($img, 0, 0, $bgcolor);

$charpool = "abcdefghijklmnopqrstuvwxyz0123456789";

$captcha = '';

// 绘制验证码主体
for ($i=0;$i<4;$i++)
{
    $fontSize = 10;
    $fontColor = imagecolorallocate($img, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
    $randomChar = substr($charpool, mt_rand(0, strlen($charpool)), 1);
    $captcha .= $randomChar;

    $x = ($i * 100 / 4) + mt_rand(5, 10);
    $y = mt_rand(5, 10);

    imagestring($img, $fontSize, $x, $y, $randomChar, $fontColor);
}

//4.3 设置背景干扰元素
for ($i = 0; $i < 200; $i++) {
    $pointcolor = imagecolorallocate($img, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
    imagesetpixel($img, mt_rand(1, 99), mt_rand(1, 29), $pointcolor);
}

//4.4 设置干扰线
for ($i = 0; $i < 3; $i++) {
    $linecolor = imagecolorallocate($img, mt_rand(50, 200), mt_rand(50, 200), mt_rand(50, 200));
    imageline($img, mt_rand(1, 99), mt_rand(1, 29), mt_rand(1, 99), mt_rand(1, 29), $linecolor);
}


// $_SESSION["captcha"] = $captcha;

// 'samesite' => 'None' // None || Lax  || Strict

setcookie('captcha', md5(strtolower($captcha)), [
    'expires' => 0,
    'samesite' => 'None',
    'secure' => true,
]);

header('content-type:image/png');

// 输出图片
imagepng($img);

imagedestroy($img);


?>