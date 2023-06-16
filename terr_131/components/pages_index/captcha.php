<?php
header ("Content-type: image/png");

$velikost=5;

$vyska=imagefontheight($velikost)+20;
$sirka=imagefontwidth($velikost);
$sirka=$sirka*5+20;

$obrazek=imagecreate($sirka, $vyska);

$barva1=imagecolorallocate($obrazek, 255, 255, 255);
$barva2=imagecolorallocate($obrazek, 215, 0, 0);

imagestring($obrazek, $velikost, 10, 10, substr(md5($_GET["hash"]), 0, 5), $barva2);

$obrazek2=imagecreatetruecolor(100, 25);
imagecopyresampled($obrazek2, $obrazek, 0, 0, 0, 0, 100, 25, $sirka, $vyska);

imagepng($obrazek2);

imagedestroy($obrazek);
imagedestroy($obrazek2);
?>