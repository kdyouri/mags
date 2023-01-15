<?php
/**
 * @var View $this
 * @var array $numero
 * @var integer $num
 */

App::uses('PageLoader', 'Lib');

$url = PageLoader::getUrl($num, $numero);//debug($url);die;
$data = file_get_contents($url);
$im = @imagecreatefromstring($data);

if ($num > 1 && $numero['Numero']['pivoter']) {
    $rotate = imagerotate($im, $numero['Numero']['pivoter'], 0);

    imagedestroy($im);
    $im = $rotate;
}

switch (true) {
    case $numero['Numero']['livret']:
    case $numero['Numero']['diviser'] && $num > 1 && $num < $numero['Numero']['nbr_pages']:
        if ($im !== false) {
            $src = $im;
            $w = imagesx($src);
            $h = imagesy($src);
            $rect = ($num % 2) ?
                ['x' => $w / 2, 'y' => 0, 'width' => $w / 2, 'height' => $h] :
                ['x' => 0, 'y' => 0, 'width' => $w / 2, 'height' => $h];
            $im = imagecrop($src, $rect);
            imagedestroy($src);
        }
}
//debug($numero);
//debug($num);
//debug($isTheFirstSheet);
//debug($url);
//die;

if ($im === false) {
    $im = imagecreatefromjpeg(IMAGES . 'no-image.jpg');
    header('Cache-Control: public, max-age=0');

} else {
    header('Cache-Control: public, max-age=31536000');
}

header('Content-Type: image/jpeg');

imagejpeg($im);
imagedestroy($im);

flush();