<?php
require './vendor/autoload.php';

use Mhor\MediaInfo\MediaInfo;

$mediaInfo = new MediaInfo();
$mediaInfoContainer = $mediaInfo->getInfo('https://lgblinks.com/dl_4664614/35303.mp4');
$general = $mediaInfoContainer->getGeneral();
echo json_encode($general);
