<?php


use Mhor\MediaInfo\MediaInfo;
try{
$mediaInfo = new MediaInfo();
$mediaInfoContainer = $mediaInfo->getInfo('https://mytgup.herokuapp.com/AOyIzN4YzN5UTM/406847488/_%D9%85%D8%A8%D8%A7%D8%B4%D8%B1_%D9%85%D8%B9_%D8%AD%D8%B4%D9%8A%D8%B4_%D9%88_%D9%87%D8%AA%D9%84%D8%B1_%D9%88_%D9%81%D8%B1%D9%8A%D8%B2%D8%B1_%F0%9F%94%A5_THE_7H_LEGENDS_ARE_BACK_5314349956612491451.mp4');
print_r($mediaInfoContainer);
}catch(exception $e){
echo "boom";
}
