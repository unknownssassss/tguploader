<?php
$updates = file_get_contents("php://input");
if(empty($updates))
{
return;
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,            "http://mahdi-kh.ir/neenja/Neenja.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt($ch, CURLOPT_POST,           1 );
curl_setopt($ch, CURLOPT_POSTFIELDS,     $updates); 
curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: application/x-www-form-urlencoded'));
$result=curl_exec ($ch);
