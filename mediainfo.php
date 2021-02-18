<?php
require './vendor/autoload.php';

use Mhor\MediaInfo\MediaInfo;

$mediaInfo = new MediaInfo();
$mediaInfoContainer = $mediaInfo->getInfo('./4_6005766620989896803.mp3');
$general = $mediaInfoContainer->getGeneral();
echo json_encode($mediaInfoContainer);
