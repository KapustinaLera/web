<?php
declare(strict_types=1);

session_start();

$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$length = rand(5, 6);
$captcha = '';
for ($i = 0; $i < $length; $i++) {
    $captcha .= $chars[random_int(0, strlen($chars) - 1)];
}

$_SESSION['captcha'] = $captcha;

$image = imagecreatefromjpeg(__DIR__ . '/noise.jpg');
$width = imagesx($image);

$baseColor = imagecolorallocate($image, 0, 0, 0);

$fonts = [
    __DIR__ . '/fonts/bellb.ttf',
    __DIR__ . '/fonts/georgia.ttf',
];

$x = 20;   
$y = 30;   

for ($i = 0; $i < strlen($captcha); $i++) {
    $char = $captcha[$i];

    $size  = rand(18, 30);  
    $angle = rand(-30, 30);               
    $font  = $fonts[array_rand($fonts)];   

    $color = imagecolorallocate(
        $image,
        random_int(0, 80),
        random_int(0, 80),
        random_int(0, 80)
    );

    // Уменьшенный разброс по высоте 
    $yPos = $y + rand(0, 5);

    imagettftext($image, $size, $angle, $x, $yPos, $color, $font, $char);

    $x += 40;
}

header('Content-Type: image/jpeg');
imagejpeg($image, null, 50);

imagedestroy($image);
