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
return "done : ".$domain[0];
}
    else {
        fclose($file);
        $status = ($stoptime - $starttime) * 1000;
        $status = floor($status);
        $gg = file_get_contents("m.txt");
        $gg .= $domain[0].":".$domain[1].PHP_EOL;
        file_put_contents("m.txt",$gg);
    }
    return $status."-".$domain[0];
}catch(Error $e){
return $e->getMessage();
}
}
$list = file_get_contents("IpTester");
$m = explode("\n",$list);
$gecko = 1;
$mozilla = 0;
foreach($m as $proxy){
$post_url = 'https://t.me/skyteam/430';
$post_url .= '?embed=1';

$gecko = 1;
$mozilla = 0;
    $user_agent = 'User-Agent: Mozilla/5.'.$mozilla.'(X11; Linux x86_64; rv:52.0) Gecko/'.$gecko.' Firefox/52.'.$mozilla;
$gecko++;
$mozilla++;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['User-Agent: '.$user_agent]);
    curl_setopt($ch, CURLOPT_URL, $post_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_PROXY, $proxy);
   // curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);

    $result = curl_exec($ch);

    if ($result === false) {
        echo 'bad proxy'.PHP_EOL;
        curl_close($ch);
        continue;
    }

    preg_match('/data-view="(\w+)"/', $result, $matches);
    preg_match('/stel_ssid=(\w+)/', $result, $session);
    $ssid = $session[1];

    curl_setopt($ch, CURLOPT_URL, $post_url.'&view='.$matches[1]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Requested-With: XMLHttpRequest', 'Cookie: stel_ssid='.$ssid, 'User-Agent: '.$user_agent]);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response_content = curl_exec($ch);

    if ($result === false) {
        echo 'Bad response'.PHP_EOL;
    } else {
        echo "OK";
    }

    curl_close($ch);
    
}

    
