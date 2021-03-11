<?php
set_time_limit(0);
ini_set('memory_limit', -1);
ini_set('max_execution_time', -1);
function pingD($domain){

try{
$domain = explode(":",$domain);
    $starttime = microtime(true);
    $file      = fsockopen ($domain[0], $domain[1], $errno, $errstr, 10);
    $stoptime  = microtime(true);
    $status    = 0;

    if (!$file){
return "done : ".$domain[0].":".$domain[1];
}
    else {
        fclose($file);
        $status = ($stoptime - $starttime) * 1000;
        $status = floor($status);
    }
    return $status."-".$domain[0].":".$domain[1];
}catch(Error $e){
return $e->getMessage();
}
}
$list = file_get_contents("IpTester");
$list = explode("\n",$list);
foreach($list as $proxy){
echo pingD($proxy).PHP_EOL;
}

    
