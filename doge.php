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
        "start" => "Hi %s, please send me any direct download url",
        "large" => "Sorry! I can't upload files that are larger than 1Gb . File size detected %s",
        "unable" => "Unable to download file.\nStatus Code : %s",
        "proc" => "proccessing....!",
        "dl" => "Downloading...\nFilename: %s\nDone: %s\nSpeed: %s\nPercentage: %s\nETA: %s\n[%s]",
        'invalid' => "URL format is incorrect. make sure your url starts with either http:// or https://",
        "filesize" => "Unable to obtain file size %s",
        "getinfo" => "Error on get URL info"
    ];
    private function geturlinfo($url) {
        /*    exec("ffprobe -v error -select_streams v:0 -show_entries stream=width,height,duration,bit_rate -of default=noprint_wrappers=1 $url",$output,$result);*/
        /*$output = yield $this->runexec("ffprobe -v error -select_streams v:0 -show_entries stream=width,height,duration,bit_rate -of default=noprint_wrappers=1 $url");*/
        $output = yield $this->runexec("curl -s -o /dev/null -D -$url");
        if (empty($output)) {
            return [];
        }
        //    return explode("=",implode("\n",$output));
        return $output;
    }
    private static function printf_array($arr) {
        return call_user_func_array('sprintf', $arr);
    }

    private function get($key, array $value) {

        return self::printf_array(array_merge([self::$array[$key] ?? null], $value));
    }
    private $admin = array(1314349655);
    private $botid = 741849360;
    public function Is_Mod($id) {
        if (!in_array($id, $this->admin)) {
            return false;
        }
        return true;
    }
    private function formatBytes($bytes, $precision = 2)

    {

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    public function ProgRe($empty, $fill, $min, $max = 100, $length = 10, $join = '')
    {
        $pf = round($min / $max * $length);
        $pe = $length - $pf;
        $pe = $pe == 0 ? '' : str_repeat($empty, $pe);
        $pf = $pf == 0 ? '' : str_repeat($fill, $pf);
        return $pf . $join . $pe;
        unset($pf);
        unset($pe);
    }
    public function XForEta($mis)
    {
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
            if ($message == "/start") {
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("start", [$from_id]), 'reply_to_msg_id' => $mid]);
                return;
            }
            if ($message == "reload") {
                yield $this->restart();
            }
            if (!filter_var($message, FILTER_VALIDATE_URL)) {
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("invalid", []), 'reply_to_msg_id' => $mid]);
                return;
            }
            $http = (new HttpClientBuilder)
            ->followRedirects(10)
            ->retry(3)
            ->build();
            $request = new Request($message);
            $response = yield $http->request($request);
            if ($response->getStatus() != 200) {
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("unable", [$response->getStatus()]), 'reply_to_msg_id' => $mid]);
                unset($http, $request, $response);
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
            if ($headers['content-length'][0] / 1024 / 1024 >= 1000) {
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("large", [$this->formatBytes($headers['content-length'][0])]), 'reply_to_msg_id' => $mid]);
                return;
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
            unset($proc, $process, $request);
            $request = new Request("https://poker-mahdi.farahost.xyz/erf/mime/Mime/?type=toext&find=".$headers['content-type'][0]);
            $response = yield $http->request($request);
            $result = json_decode((yield $response->getBody()->buffer()), true);
            
            $time2 = time();

                $url = new \danog\MadelineProto\FileCallback($message, function ($progress) use ($peer,$headers, $time2,$result,$id)

                {
                    static $prev = 0;
                    $now = \time();
                    if ($now - $prev < 10 && $progress < 100)
                    {
                        return;
                    }
                    $filename = md5($message).".".$result['result'];
                        $filesize = $headers['content-length'];
                    $time3 = time() - $time2;
                    $prev = $now;
                    $current = $progress / 100 * $filesize;
                    $speed = ($current == 0 ? 1 : $current) / ($time3 == 0 ? 1 : $time3) ;
                    $elap = round($time3) * 1000;
                    $ttc = round(($filesize - $current) / $speed) * 1000;
                    $ett = $this->XForEta($elap + $ttc);
                    $k = ["⏳", "⌛"];
                    try
                    {
                        
                        $tmp = "File : " . $filename . "\nDownloading : " . round($progress) . "%\n[" . $this->ProgRe("▫️", "◾️", $progress, 100, 10, "") . $k[array_rand($k) ] . "]\n" . $this->formatBytes($current) . " of " . $this->formatBytes($filesize) . "\nSpeed : " . $this->formatBytes($speed) . "/Sec\nETA : " . $this->XForEta($elap) . " / " . $ett . "\n@SkyTeam";
                        yield $this
                            ->messages
                            ->editMessage(['peer' => $peer, 'message' => $tmp, 'id' => $id, 'parse_mode' => "MarkDown"], ['FloodWaitLimit' => 0]);
                    }
                    catch(\Throwable $e)
                    {
                        yield $this->report($e->getMessage());
                    }
                });
            
            
            
            
    /*        $url = new \danog\MadelineProto\FileCallback(
                $message,
                function ($progress, $speed, $time) use ($peer, $mid, $id) {
                    static $prev = 0;
                    $now = \time();
                    if ($now - $prev < 10 && $progress < 100) {
                        return;
                    }
                    $prev = $now;
                    try {
                        yield $this->messages->editMessage(['peer' => $peer, 'id' => $id, 'message' => "Upload progress: $progress%\nSpeed: $speed mbps\nTime elapsed since start: $time"], ['FloodWaitLimit' => 0]);
                    } catch (\danog\MadelineProto\RPCErrorException $e) {}
                }
            );*/
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
                'message' => $message,
                'parse_mode' => 'Markdown'
            ];
            if (isset($m[1]) && isset($m[2])) {
                $combine = array_combine($m[1], $m[2]);
                if (isset($combine['duration']) && isset($combine['width']) && isset($combine['height'])) {
                    $attribute = ['peer' => $peer,
                        'media' => ['_' => 'inputMediaUploadedDocument',
                            'file' => $url,
                            'attributes' => [
                                ['_' => 'documentAttributeVideo',
                                    'round_message' => false,
                                    'supports_streaming' => true,
                                    'duration' => $combine['duration'],
                                    'w' => $combine['width'],
                                    'h' => $combine['height']]
                            ]],
                        'message' => $message,
                        'reply_to_msg_id' => $mid];
                }
            }
            yield $this->messages->sendMedia($attribute);

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
