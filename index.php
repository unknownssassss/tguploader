<?php
set_time_limit(0);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 800);
$updates = file_get_contents("php://input");
if(empty($updates))
{
return;
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,            "http://testtt.rf.gd/Neenja.php");
curl_setopt($ch, CURLOPT_POST,           1 );
curl_setopt($ch, CURLOPT_POSTFIELDS,     $updates); 
curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain'));
curl_exec ($ch);
curl_close($ch);
