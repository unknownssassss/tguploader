<?php
set_time_limit(0);
ini_set('memory_limit', -1);
ini_set('max_execution_time', -1);
function fetchdata($channel, $postid, $proxy) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_URL, "https://t.me/$channel/$postid?embed=1");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo curl_error($ch).PHP_EOL;
    }
    curl_close($ch);
    $m = explode('data-view="', $result);
    if (!isset($m[1])) {
        return false;
    }
    $m = explode('"', $m[1]);
    if (!isset($m[0])) {
        return false;
    }
    echo "get Key from url ".$m[1];
    $array = [];
    $array['key'] = $m[0];
    preg_match_all("/Set\-Cookie\:(.*)/", $result, $cook);
    if (!isset($cook[1])) {
        return false;
    }
    $cook = explode(";", implode("\n", array_values($cook[1])));
    if (!isset($cook[0])) {
        return false;
    }
    echo "Get Cookie From Url ".$cook[0];
    $array['cookie'] = $cook[0];

    return $array;
}
function addViewToPost($channel,$postid,$key, $cookie, $proxy) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_URL, "https://t.me/v/?views=$key");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Requested-With: XMLHttpRequest', 'User-Agent: Mozilla/5.0 (Windows NT 6.2; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0', 'Referer: https://t.me/'.$channel.'/'.$postid.'?embed=1', 'Cookie: '.$cookie]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function run($channel, $postid, $proxy) {
    $fetch = fetchdata($channel, $postid, $proxy);
    if (is_array($fetch) && isset($fetch['key']) && isset($fetch['cookie'])) {
        $result = addViewToPost($channel,$postid,$fetch['key'], $fetch['cookie'], $proxy);
        if ($result) {
            return "proxy $proxy finished its job successfully";
        } else {
            return 'Thread with proxy '.$proxy.' has been terminated';
        }
    } else {
        return 'cant Fetch Data '.$proxy;
    }
}
$list = explode("\n", file_get_contents("IpTester"));
foreach ($list as $proxy) {
    echo run("skyteam", 430, $proxy).PHP_EOL;
}
