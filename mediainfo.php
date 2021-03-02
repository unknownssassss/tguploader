<?php
require './vendor/autoload.php';

use Mhor\MediaInfo\MediaInfo;

$mediaInfo = new MediaInfo();
$mediaInfoContainer = $mediaInfo->getInfo('https://lgblinks.com/dl_4863206/Project_02-28_SD%20360p.mp4');
$general = $mediaInfoContainer->getGeneral();
echo json_encode($mediaInfoContainer);
