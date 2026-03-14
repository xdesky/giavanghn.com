<?php
$img = imagecreatefrompng('public/images/banks/bank-sprites.png');
echo 'Width: '.imagesx($img).' Height: '.imagesy($img)."\n";

// Check if there's any non-transparent content
$hasContent = false;
for ($x = 0; $x < imagesx($img) && !$hasContent; $x++) {
    for ($y = 0; $y < imagesy($img) && !$hasContent; $y++) {
        $rgba = imagecolorat($img, $x, $y);
        $alpha = ($rgba >> 24) & 0x7F; // 127 = fully transparent, 0 = opaque
        if ($alpha < 127) {
            $hasContent = true;
        }
    }
}
echo $hasContent ? "Image has visible content\n" : "Image appears fully transparent!\n";
