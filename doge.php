<?php
set_time_limit(0);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 800);
date_default_timezone_set("Asia/tehran");
if (!\file_exists('madeline.php')) {
    \copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
require_once('madeline.php');
use danog\MadelineProto\API;
use Amp\File\File;
use Amp\Http\Client\Interceptor\LogHttpArchive;
use Amp\Http\Client\Interceptor\MatchOrigin;
use Amp\Http\Client\Interceptor\SetRequestHeader;
use danog\MadelineProto\Exception;
use danog\MadelineProto\Logger;
use Amp\ByteStream;
use Amp\Process\Process;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\RPCErrorException;
use danog\MadelineProto\MTProto;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\HttpException;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
class MrPoKeR extends EventHandler
{
    private static $array = [
        "start" => "Hi, please send me any direct download url",
        "large" => "Sorry! I can't upload files that are larger than 2Gb . File size detected %s",
        "unable" => "Unable to download file.\nStatus Code : %s",
        "proc" => "Processing your request...",
        "dl" => "Downloading...\nFilename: %s\nDone: %s\nSpeed: %s\nPercentage: %s\nETA: %s\n[%s]",
        'invalid' => "URL format is incorrect. make sure your url starts with either http:// or https://",
        "filesize" => "Unable to obtain file size %s",
        "getinfo" => "Error on get URL info"
    ];

    private static function printf_array($arr) {
        return call_user_func_array('sprintf', $arr);
    }
    private function onprog($link, $mid, $peer, $filesize, $filename, $ext, $id, $mime, $duration = null, $height = null, $width = null,$thumb) {
        $time2 = time();
        $url = new \danog\MadelineProto\FileCallback($link, function ($progress) use ($peer, $link, $time2, $msgid, $filename, $filesize, $ext, $id) {
            static $prev = 0;
            $now = \time();
            if ($now - $prev < 10 && $progress < 100) {
                return;
            }
            $time3 = time() - $time2;
            $prev = $now;
            $current = $progress / 100 * $filesize;
            $speed = ($current == 0 ? 1 : $current) / ($time3 == 0 ? 1 : $time3);
            $elap = round($time3) * 1000;
            $ttc = round(($filesize - $current) / $speed) * 1000;
            $ett = $this->XForEta($elap + $ttc);
            $k = ["⏳",
                "⌛"];
            try
            {
                $tmp = "File : " . $filename . ".$ext\nDownloading : " . round($progress) . "%\n[" . $this->ProgRe("️○", "●", $progress, 100, 10, "") . $k[array_rand($k)] . "]\n" . $this->formatBytes($current) . " of " . $this->formatBytes($filesize) . "\nSpeed : " . $this->formatBytes($speed) . "/Sec\nETA : " . $this->XForEta($elap) . " / " . $ett . "\n@SkyTeam";
                yield $this
                ->messages
                ->editMessage(['peer' => $peer, 'message' => $tmp, 'id' => $id, 'parse_mode' => "MarkDown"], ['FloodWaitLimit' => 0]);
            }catch(\Throwable $e) {
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => preg_replace("/!!! WARNING !!!
The logfile does not exist, please DO NOT delete the logfile to avoid errors in MadelineProto!/", "", $e->getMessage()), 'reply_to_msg_id' => $mid]);
                return;
            }
        });
        try {
            $attribute = [
                'peer' => $peer,
                'reply_to_msg_id' => $mid,
                'media' => [
                    '_' => 'inputMediaUploadedDocument',
                    'file' => $url,
                    'attributes' => [
                        ['_' => 'documentAttributeFilename',
                            'file_name' => $filename.".".$ext]
                    ]
                ],
                'message' => "@skyteam",
                'parse_mode' => 'Markdown'
            ];
            if(!is_null($duration) && !is_null($height) && !is_null($width) && !preg_match("/image/", $mime)){
                $attribute = ['peer' => $peer,
                        'media' => ['_' => 'inputMediaUploadedDocument',
                            'file' => $url,
                            'thumb' => file_exists($thumb) ? $thumb : "https://gettgfile.herokuapp.com/aieegjediaf_chijcjgcfi/400098000119_385156.jpg",
                            'attributes' => [
                                ['_' => 'documentAttributeVideo',
                                    'round_message' => false,
                                    'supports_streaming' => true,
                                   'duration' => $duration,
                                    'w' => $width,
                                    'h' => $height]
                            ]],
                        'message' => "@skyteam",
                        'reply_to_msg_id' => $mid];
            }
            yield $this->messages->sendMedia($attribute);
            return;
        }catch(\Throwable $e) {
            yield $this->messages->sendMessage(['peer' => $peer, 'message' => preg_replace("/!!! WARNING !!!
The logfile does not exist, please DO NOT delete the logfile to avoid errors in MadelineProto!/", "", $e->getMessage()), 'reply_to_msg_id' => $mid]);
            return;
        }
    }
    private function get($key,
        array $value) {

        return self::printf_array(array_merge([self::$array[$key] ?? null], $value));
    }
    private function catchYt($link) {
        try {
            $get = yield $this->fileGetContents("https://ytubecom.herokuapp.com/api/info?url=".$link);
            $get = json_decode($get,
                true);
            if (!isset($get['info']['formats'])) {
                return ['result' => null];
            }
            return count($get['info']['formats'] == 0 ? ['result' => null] : ['result' => $get['info']['formats']]);
        }catch(\Throwable $e) {
            return ['result' => null];
        }
    }
    private $admin = array(1314349655);
    private $botid = 741849360;
    public function Is_Mod($id) {
        if (!in_array($id, $this->admin)) {
            return false;
        }
        return true;
    }
    private function formatBytes($bytes, $precision = 2) {

        $units = ['B',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
            'EB',
            'ZB',
            'YB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function ProgRe($empty, $fill, $min, $max = 100, $length = 10, $join = '') {
        $pf = round($min / $max * $length);
        $pe = $length - $pf;
        $pe = $pe == 0 ? '' : str_repeat($empty, $pe);
        $pf = $pf == 0 ? '' : str_repeat($fill, $pf);
        return $pf . $join . $pe;
        unset($pf);
        unset($pe);
    }
    private function RequesttoUrl($url) {
        try {
            $http = (new HttpClientBuilder)
            ->followRedirects(10)
            ->retry(3)
            ->build();
            $request = new Request($url);
            $response = yield $http->request($request);
            return $response;
        }catch(\Throwable $e) {
            return ['result' => $e->getMessage()];
        }
    }
    public function XForEta($mis) {
        $seconds = $mis / 1000;
        $mils = round($mis % 1000);
        $minutes = $seconds / 60;
        $seconds = round($seconds % 60);
        $hours = $minutes / 60;
        $minutes = round($minutes % 60);
        $days = round($hours / 24);
        $hours = round($hours % 24);
        $tmp = (($days ? $days . " Day | " : "") . "" . ($hours ? $hours . " H " : "") . "" . ($minutes ? $minutes . " Min " : "") . "" . ($seconds ? $seconds . " Sec " : "") . "" . ($mils ? $mils . " Ms " : ""));
        return $tmp;
    }
    public function onUpdateNewChannelMessage($update) {
        yield $this->onUpdateNewMessage($update);
    }
    private function runexec($cmd) {
        $process = new Process($cmd);
        yield $process->start();

        $proc = yield ByteStream\buffer($process->getStdout());
        return $proc;
    }
    private function ValidYoutube($link) {
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:(?:v|e(?:mbed)?)/|.*[?&]v=|[^/]+/.+/)|youtu\.be/)([^"&?/ ]{11})%i', $link, $m)) {
            return $m[1];
        }
        return false;
    }
    public function getReportPeers() {
        return ['mehtiw_kh'];
    }
    public function onUpdateNewMessage($update) {
        if (isset($update['message']) && $update['message']['out'] ?? false) {
            return;
        }
        if ($update['message']['date'] < time() - 60) {
            return;
        }   $message = isset($update['message']['message']) ? $update['message']['message'] : null;
        $mid = isset($update['message']['id']) ? $update['message']['id'] : null;
        $from_id = isset($update['message']['from_id']) ? $update['message']['from_id'] : null;
        try {
            $getallinfo = yield $this->getInfo($update);
            $peer = $getallinfo['bot_api_id'];
            if (preg_match("/^(run)\s+(.+)$/is", $message, $match)) {
                try {
                    ob_start();
                    eval($match[2].'?>');
                    $run = ob_get_contents();
                    ob_end_clean();
                }catch(Exception $e) {
                    $run = $e->getMessage().PHP_EOL."Line :".$e->getLine();
                }catch(ParseError $e) {
                    $run = $e->getMessage().PHP_EOL."Line :".$e->getLine();
                }catch(FatalError $e) {
                    $run = $e->getMessage().PHP_EOL."Line :".$e->getLine();
                }
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => "Code :\n".$match[2]."\nResult : \n".strip_tags($run)."\n"]);
                unset($run);
                return;
            }
            if ($message == "/start") {
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("start", []), 'reply_to_msg_id' => $mid]);
                return;
            }
            if ($message == "reload") {
                yield $this->restart();
            }
            if (!filter_var($message, FILTER_VALIDATE_URL)) {
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("invalid", []), 'reply_to_msg_id' => $mid]);
                return;
            }
            try {
                $response = yield $this->RequesttoUrl($message);
                if (is_array($response) && isset($response['result'])) {
                    yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("getinfo", []), 'reply_to_msg_id' => $mid]);
                    return;
                }
                if ($response->getStatus() != 200) {
                    yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("unable", [$response->getStatus()]), 'reply_to_msg_id' => $mid]);
                    unset($response);
                    return;
                }
                $headers = $response->getHeaders();
                if (!isset($headers['content-length'][0])) {

                    yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("filesize", [$message]), 'reply_to_msg_id' => $mid]);
                    return;
                }
                if (!isset($headers['content-type'][0])) {
                    yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("getinfo", []), 'reply_to_msg_id' => $mid]);
                    return;
                }
                if ($headers['content-length'][0] / 1024 / 1024 >= 2000) {
                    yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("large", [$this->formatBytes($headers['content-length'][0])]), 'reply_to_msg_id' => $mid]);
                    return;
                }
                if (!file_exists(md5($message).".png")) {
                    $process = new Process("ffmpeg -i$message -ss 00:00:01.000 -vframes 1 ".md5($message).".png");
                    yield $process->start();
                    $proc = (yield ByteStream\buffer($process->getStdout()));
                    unset($proc, $process);
                }
                $id = yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("proc", []), 'reply_to_msg_id' => $mid]);
                if (!isset($id['id'])) {
                    $this->report(\json_encode($id));
                    foreach ($id['updates'] as $updat) {
                        if (isset($updat['id'])) {
                            $id = $updat['id'];
                            break;
                        }
                    }
                } else {
                    $id = $id['id'];
                }
                $process = new Process("ffprobe -v error -show_format -show_streams ".$message);
                yield $process->start();
                $proc = (yield ByteStream\buffer($process->getStdout()));
                preg_match_all("/(.*)[\:|\=](.*)/", $proc, $m);
                unset($proc, $process);
                $request = new Request("https://poker-mahdi.farahost.xyz/erf/mime/Mime/?type=toext&find=".$headers['content-type'][0]);
                $response = yield $http->request($request);
                $result = json_decode((yield $response->getBody()->buffer()), true);
                $combine = [];
                if (isset($m[1]) && isset($m[2])) {
                    $combine = array_combine($m[1], $m[2]);
                }
                yield $this->onprog($message,$mid,$peer,$headers['content-length'][0],md5($message),$result['result'],$id,$headers['content-type'][0],isset($combine['duration']) ? $combine['duration'] : null,isset($combine['height']) ? $combine['height'] : null,isset($combine['width']) ? $combine['width'] : null,md5($message).".png");
      /*          $time2 = time();

                $url = new \danog\MadelineProto\FileCallback($message, function ($progress) use ($peer, $headers, $message, $time2, $result, $id) {
                    static $prev = 0;
                    $now = \time();
                    if ($now - $prev < 10 && $progress < 100) {
                        return;
                    }
                    $filename = md5($message).".".$result['result'];
                    $filesize = $headers['content-length'][0];
                    $time3 = time() - $time2;
                    $prev = $now;
                    $current = $progress / 100 * $filesize;
                    $speed = ($current == 0 ? 1 : $current) / ($time3 == 0 ? 1 : $time3);
                    $elap = round($time3) * 1000;
                    $ttc = round(($filesize - $current) / $speed) * 1000;
                    $ett = $this->XForEta($elap + $ttc);
                    $k = ["⏳",
                        "⌛"];
                    try
                    {

                        $tmp = "File : " . $filename . "\nDownloading : " . round($progress) . "%\n[" . $this->ProgRe("️○", "●", $progress, 100, 10, "") . $k[array_rand($k)] . "]\n" . $this->formatBytes($current) . " of " . $this->formatBytes($filesize) . "\nSpeed : " . $this->formatBytes($speed) . "/Sec\nETA : " . $this->XForEta($elap) . " / " . $ett . "\n@SkyTeam";
                        yield $this
                        ->messages
                        ->editMessage(['peer' => $peer, 'message' => $tmp, 'id' => $id, 'parse_mode' => "MarkDown"], ['FloodWaitLimit' => 0]);
                    }
                    catch(\Throwable $e) {
                        yield $this->messages->sendMessage(['peer' => $peer, 'message' => preg_replace("/!!! WARNING !!!
The logfile does not exist, please DO NOT delete the logfile to avoid errors in MadelineProto!/", "", $e->getMessage()), 'reply_to_msg_id' => $mid]);
                        return;
                    }
                });
                $attribute = [
                    'peer' => $peer,
                    'reply_to_msg_id' => $mid,
                    'media' => [
                        '_' => 'inputMediaUploadedDocument',
                        'file' => $url,
                        'attributes' => [
                            ['_' => 'documentAttributeFilename',
                                'file_name' => md5($message).".".$result['result']]
                        ]
                    ],
                    'message' => "@skyteam",
                    'parse_mode' => 'Markdown'
                ];
                if (isset($m[1]) && isset($m[2])) {
                    $combine = array_combine($m[1], $m[2]);
                    if (isset($combine['duration']) && isset($combine['width']) && isset($combine['height']) && !preg_match("/image/", $headers['content-type'][0])) {
                        $attribute = ['peer' => $peer,
                            'media' => ['_' => 'inputMediaUploadedDocument',
                                'file' => $url,
                                'thumb' => file_exists(md5($message).".png") ? md5($message).".png" : "https://gettgfile.herokuapp.com/aieegjediaf_chijcjgcfi/400098000119_385156.jpg",
                                'attributes' => [
                                    ['_' => 'documentAttributeVideo',
                                        'round_message' => false,
                                        'supports_streaming' => true,
                                        'duration' => $combine['duration'],
                                        'w' => $combine['width'],
                                        'h' => $combine['height']]
                                ]],
                            'message' => "@skyteam",
                            'reply_to_msg_id' => $mid];
                    }
                }
                yield $this->messages->sendMedia($attribute);*/
                
            }catch(\Throwable $e) {
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => preg_replace("/!!! WARNING !!!
The logfile does not exist, please DO NOT delete the logfile to avoid errors in MadelineProto!/", "", $e->getMessage()), 'reply_to_msg_id' => $mid]);
                return;
            }
        } catch(\Throwable $e) {
            yield $this->report("➲Error :".$e->getMessage()."\n".$e->getLine()."\n".$e->getFile());
        }
    }
}

$settings = [];
$settings['serialization']['serialization_interval'] = 60 * 6;
$settings['logger']['logger_level'] = 5;
$settings['logger']['logger'] = \danog\MadelineProto\Logger::FILE_LOGGER;
$settings['logger']['max_size'] = 2 * 1024 *1024;
$settings['peer']['cache_all_peers_on_startup'] = true;
$settings['serialization']['cleanup_before_serialization'] = true;
$mProto = new API("upload.madeline", $settings);
$mProto->startAndLoop(MrPoKeR::class);
