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
    private $storagechannel = "";
    private $botusers = array();
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
    public function onUpdateBotInlineQuery($update) {

        yield $this->onUpdateNewMessage($update);

    }
    public function onUpdateBotInlineSend($update) {
        yield $this->onUpdateNewMessage($update);
    }
    public function onUpdateBotCallbackQuery($update) {
        yield $this->onUpdateNewMessage($update);
    }
    public function onUpdateInlineBotCallbackQuery($update) {
        yield $this->onUpdateNewMessage($update);
    }
    private function itag($itag) {
        $_formats = array(
            '5' => array('ext' => 'flv', 'width' => 400, 'height' => 240, 'acodec' => 'mp3', 'abr' => 64, 'vcodec' => 'h263'),
            '6' => array('ext' => 'flv', 'width' => 450, 'height' => 270, 'acodec' => 'mp3', 'abr' => 64, 'vcodec' => 'h263'),
            '13' => array('ext' => '3gp', 'acodec' => 'aac', 'vcodec' => 'mp4v'),
            '17' => array('ext' => '3gp', 'width' => 176, 'height' => 144, 'acodec' => 'aac', 'abr' => 24, 'vcodec' => 'mp4v'),
            '18' => array('ext' => 'mp4', 'width' => 640, 'height' => 360, 'acodec' => 'aac', 'abr' => 96, 'vcodec' => 'h264'),
            '22' => array('ext' => 'mp4', 'width' => 1280, 'height' => 720, 'acodec' => 'aac', 'abr' => 192, 'vcodec' => 'h264'),
            '34' => array('ext' => 'flv', 'width' => 640, 'height' => 360, 'acodec' => 'aac', 'abr' => 128, 'vcodec' => 'h264'),
            '35' => array('ext' => 'flv', 'width' => 854, 'height' => 480, 'acodec' => 'aac', 'abr' => 128, 'vcodec' => 'h264'),
            # itag 36 videos are either 320x180 (BaW_jenozKc) or 320x240 (__2ABJjxzNo), abr varies as well
            '36' => array('ext' => '3gp', 'width' => 320, 'acodec' => 'aac', 'vcodec' => 'mp4v'),
            '37' => array('ext' => 'mp4', 'width' => 1920, 'height' => 1080, 'acodec' => 'aac', 'abr' => 192, 'vcodec' => 'h264'),
            '38' => array('ext' => 'mp4', 'width' => 4096, 'height' => 3072, 'acodec' => 'aac', 'abr' => 192, 'vcodec' => 'h264'),
            '43' => array('ext' => 'webm', 'width' => 640, 'height' => 360, 'acodec' => 'vorbis', 'abr' => 128, 'vcodec' => 'vp8'),
            '44' => array('ext' => 'webm', 'width' => 854, 'height' => 480, 'acodec' => 'vorbis', 'abr' => 128, 'vcodec' => 'vp8'),
            '45' => array('ext' => 'webm', 'width' => 1280, 'height' => 720, 'acodec' => 'vorbis', 'abr' => 192, 'vcodec' => 'vp8'),
            '46' => array('ext' => 'webm', 'width' => 1920, 'height' => 1080, 'acodec' => 'vorbis', 'abr' => 192, 'vcodec' => 'vp8'),
            '59' => array('ext' => 'mp4', 'width' => 854, 'height' => 480, 'acodec' => 'aac', 'abr' => 128, 'vcodec' => 'h264'),
            '78' => array('ext' => 'mp4', 'width' => 854, 'height' => 480, 'acodec' => 'aac', 'abr' => 128, 'vcodec' => 'h264'),


            # 3D videos
            '82' => array('ext' => 'mp4', 'height' => 360, 'format_note' => '3D', 'acodec' => 'aac', 'abr' => 128, 'vcodec' => 'h264', 'preference' => -20),
            '83' => array('ext' => 'mp4', 'height' => 480, 'format_note' => '3D', 'acodec' => 'aac', 'abr' => 128, 'vcodec' => 'h264', 'preference' => -20),
            '84' => array('ext' => 'mp4', 'height' => 720, 'format_note' => '3D', 'acodec' => 'aac', 'abr' => 192, 'vcodec' => 'h264', 'preference' => -20),
            '85' => array('ext' => 'mp4', 'height' => 1080, 'format_note' => '3D', 'acodec' => 'aac', 'abr' => 192, 'vcodec' => 'h264', 'preference' => -20),
            '100' => array('ext' => 'webm', 'height' => 360, 'format_note' => '3D', 'acodec' => 'vorbis', 'abr' => 128, 'vcodec' => 'vp8', 'preference' => -20),
            '101' => array('ext' => 'webm', 'height' => 480, 'format_note' => '3D', 'acodec' => 'vorbis', 'abr' => 192, 'vcodec' => 'vp8', 'preference' => -20),
            '102' => array('ext' => 'webm', 'height' => 720, 'format_note' => '3D', 'acodec' => 'vorbis', 'abr' => 192, 'vcodec' => 'vp8', 'preference' => -20),

            # Apple HTTP Live Streaming
            '91' => array('ext' => 'mp4', 'height' => 144, 'format_note' => 'HLS', 'acodec' => 'aac', 'abr' => 48, 'vcodec' => 'h264', 'preference' => -10),
            '92' => array('ext' => 'mp4', 'height' => 240, 'format_note' => 'HLS', 'acodec' => 'aac', 'abr' => 48, 'vcodec' => 'h264', 'preference' => -10),
            '93' => array('ext' => 'mp4', 'height' => 360, 'format_note' => 'HLS', 'acodec' => 'aac', 'abr' => 128, 'vcodec' => 'h264', 'preference' => -10),
            '94' => array('ext' => 'mp4', 'height' => 480, 'format_note' => 'HLS', 'acodec' => 'aac', 'abr' => 128, 'vcodec' => 'h264', 'preference' => -10),
            '95' => array('ext' => 'mp4', 'height' => 720, 'format_note' => 'HLS', 'acodec' => 'aac', 'abr' => 256, 'vcodec' => 'h264', 'preference' => -10),
            '96' => array('ext' => 'mp4', 'height' => 1080, 'format_note' => 'HLS', 'acodec' => 'aac', 'abr' => 256, 'vcodec' => 'h264', 'preference' => -10),
            '132' => array('ext' => 'mp4', 'height' => 240, 'format_note' => 'HLS', 'acodec' => 'aac', 'abr' => 48, 'vcodec' => 'h264', 'preference' => -10),
            '151' => array('ext' => 'mp4', 'height' => 72, 'format_note' => 'HLS', 'acodec' => 'aac', 'abr' => 24, 'vcodec' => 'h264', 'preference' => -10),

            # DASH mp4 video
            '133' => array('ext' => 'mp4', 'height' => 240, 'format_note' => 'DASH video', 'vcodec' => 'h264', 'preference' => -40),
            '134' => array('ext' => 'mp4', 'height' => 360, 'format_note' => 'DASH video', 'vcodec' => 'h264', 'preference' => -40),
            '135' => array('ext' => 'mp4', 'height' => 480, 'format_note' => 'DASH video', 'vcodec' => 'h264', 'preference' => -40),
            '136' => array('ext' => 'mp4', 'height' => 720, 'format_note' => 'DASH video', 'vcodec' => 'h264', 'preference' => -40),
            '137' => array('ext' => 'mp4', 'height' => 1080, 'format_note' => 'DASH video', 'vcodec' => 'h264', 'preference' => -40),
            '138' => array('ext' => 'mp4', 'format_note' => 'DASH video', 'vcodec' => 'h264', 'preference' => -40), # Height can vary (https =>//github.com/rg3/youtube-dl/issues/4559)
            '160' => array('ext' => 'mp4', 'height' => 144, 'format_note' => 'DASH video', 'vcodec' => 'h264', 'preference' => -40),
            '264' => array('ext' => 'mp4', 'height' => 1440, 'format_note' => 'DASH video', 'vcodec' => 'h264', 'preference' => -40),
            '298' => array('ext' => 'mp4', 'height' => 720, 'format_note' => 'DASH video', 'vcodec' => 'h264', 'fps' => 60, 'preference' => -40),
            '299' => array('ext' => 'mp4', 'height' => 1080, 'format_note' => 'DASH video', 'vcodec' => 'h264', 'fps' => 60, 'preference' => -40),
            '266' => array('ext' => 'mp4', 'height' => 2160, 'format_note' => 'DASH video', 'vcodec' => 'h264', 'preference' => -40),

            # Dash mp4 audio
            '139' => array('ext' => 'm4a', 'format_note' => 'DASH audio', 'acodec' => 'aac', 'abr' => 48, 'preference' => -50, 'container' => 'm4a_dash'),
            '140' => array('ext' => 'm4a', 'format_note' => 'DASH audio', 'acodec' => 'aac', 'abr' => 128, 'preference' => -50, 'container' => 'm4a_dash'),
            '141' => array('ext' => 'm4a', 'format_note' => 'DASH audio', 'acodec' => 'aac', 'abr' => 256, 'preference' => -50, 'container' => 'm4a_dash'),
            '256' => array('ext' => 'm4a', 'format_note' => 'DASH audio', 'acodec' => 'aac', 'preference' => -50, 'container' => 'm4a_dash'),
            '258' => array('ext' => 'm4a', 'format_note' => 'DASH audio', 'acodec' => 'aac', 'preference' => -50, 'container' => 'm4a_dash'),

            # Dash webm
            '167' => array('ext' => 'webm', 'height' => 360, 'width' => 640, 'format_note' => 'DASH video', 'container' => 'webm', 'vcodec' => 'vp8', 'preference' => -40),
            '168' => array('ext' => 'webm', 'height' => 480, 'width' => 854, 'format_note' => 'DASH video', 'container' => 'webm', 'vcodec' => 'vp8', 'preference' => -40),
            '169' => array('ext' => 'webm', 'height' => 720, 'width' => 1280, 'format_note' => 'DASH video', 'container' => 'webm', 'vcodec' => 'vp8', 'preference' => -40),
            '170' => array('ext' => 'webm', 'height' => 1080, 'width' => 1920, 'format_note' => 'DASH video', 'container' => 'webm', 'vcodec' => 'vp8', 'preference' => -40),
            '218' => array('ext' => 'webm', 'height' => 480, 'width' => 854, 'format_note' => 'DASH video', 'container' => 'webm', 'vcodec' => 'vp8', 'preference' => -40),
            '219' => array('ext' => 'webm', 'height' => 480, 'width' => 854, 'format_note' => 'DASH video', 'container' => 'webm', 'vcodec' => 'vp8', 'preference' => -40),
            '278' => array('ext' => 'webm', 'height' => 144, 'format_note' => 'DASH video', 'container' => 'webm', 'vcodec' => 'vp9', 'preference' => -40),
            '242' => array('ext' => 'webm', 'height' => 240, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'preference' => -40),
            '243' => array('ext' => 'webm', 'height' => 360, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'preference' => -40),
            '244' => array('ext' => 'webm', 'height' => 480, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'preference' => -40),
            '245' => array('ext' => 'webm', 'height' => 480, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'preference' => -40),
            '246' => array('ext' => 'webm', 'height' => 480, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'preference' => -40),
            '247' => array('ext' => 'webm', 'height' => 720, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'preference' => -40),
            '248' => array('ext' => 'webm', 'height' => 1080, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'preference' => -40),
            '271' => array('ext' => 'webm', 'height' => 1440, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'preference' => -40),
            # itag 272 videos are either 3840x2160 (e.g. RtoitU2A-3E) or 7680x4320 (sLprVF6d7Ug)
            '272' => array('ext' => 'webm', 'height' => 2160, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'preference' => -40),
            '302' => array('ext' => 'webm', 'height' => 720, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'fps' => 60, 'preference' => -40),
            '303' => array('ext' => 'webm', 'height' => 1080, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'fps' => 60, 'preference' => -40),
            '308' => array('ext' => 'webm', 'height' => 1440, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'fps' => 60, 'preference' => -40),
            '313' => array('ext' => 'webm', 'height' => 2160, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'preference' => -40),
            '315' => array('ext' => 'webm', 'height' => 2160, 'format_note' => 'DASH video', 'vcodec' => 'vp9', 'fps' => 60, 'preference' => -40),

            # Dash webm audio
            '171' => array('ext' => 'webm', 'acodec' => 'vorbis', 'format_note' => 'DASH audio', 'abr' => 128, 'preference' => -50),
            '172' => array('ext' => 'webm', 'acodec' => 'vorbis', 'format_note' => 'DASH audio', 'abr' => 256, 'preference' => -50),

            # Dash webm audio with opus inside
            '249' => array('ext' => 'webm', 'format_note' => 'DASH audio', 'acodec' => 'opus', 'abr' => 50, 'preference' => -50),
            '250' => array('ext' => 'webm', 'format_note' => 'DASH audio', 'acodec' => 'opus', 'abr' => 70, 'preference' => -50),
            '251' => array('ext' => 'webm', 'format_note' => 'DASH audio', 'acodec' => 'opus', 'abr' => 160, 'preference' => -50),

            # RTMP (unnamed)
            '_rtmp' => array('protocol' => 'rtmp')
        );

        if (array_key_exists($itag, $_formats))
            return $_formats[$itag];
        else
            return [];
    }
    private function onprog($link, $mid, $peer, $filesize, $filename, $ext, $id, $mime, $duration = null, $height = null, $width = null, $thumb) {
        $time2 = time();
        $url = new \danog\MadelineProto\FileCallback($link, function ($progress) use ($peer, $link, $time2, $filename, $filesize, $ext, $id) {
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
            if (!is_null($duration) && !is_null($height) && !is_null($width) && !preg_match("/image/", $mime)) {
                $attribute = ['peer' => $peer,
                    'media' => ['_' => 'inputMediaUploadedDocument',
                        'file' => $url,
                        'thumb' => file_exists($thumb) or filter_var($thumb, FILTER_VALIDATE_URL) ? $thumb : "https://gettgfile.herokuapp.com/aieegjediaf_chijcjgcfi/400098000119_385156.jpg",
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
            return count($get['info']['formats']) == 0 ? ['result' => null] : $get['info'];
        }catch(\Throwable $e) {
            return ['result' => null];
        }
    }
    private function getyoutubelink($url, $q) {
        $get = yield $this->catchYt($url);
        if (isset($get['result']) && is_null($get['result'])) {
            return ['result' => null];
        }
        
        foreach ($get['formats'] as $formats) {
            if (!isset($formats['format'])) {
                continue;
            }
            if (preg_match("/$q/", $formats['format'])) {
                return ['result' => isset($formats['url']) ? $formats['url'] : null];
                break;
            }
        }
        return ['result'=>null];
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
    private function getAllGroups($ty = "chats") {
        $dialog = yield $this->getDialogs();
        $list = [];
        foreach ($dialog as $id) {
            try {
                $type = yield $this->getInfo($id);
                if ($ty == "chats") {
                    if ((isset($type['type'])) && ($type['type'] == "supergroup" or $type['type'] == "chat")) {
                        $list[] = $id;
                    }
                } elseif ($ty == 'users') {
                    if ((isset($type['type'])) && ($type['type'] == "user")) {
                        $list[] = $id;
                    }
                } else {
                    if ((isset($type['type'])) && ($type['type'] == "channel")) {
                        $list[] = $id;
                    }
                }
            }catch(RPCErrorException $e) {
                unset($e);
                continue;
            }catch(Exception $e) {
                unset($e);
                continue;
            }
        }
        return $list;
        unset($list, $dialog);
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
            unset($http, $request, $response);
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
        unset($proc, $process);
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
        if (isset($update['message']['date']) && $update['message']['date'] < time() - 60) {
            return;
        }   $message = isset($update['message']['message']) ? $update['message']['message'] : null;
        $mid = isset($update['message']['id']) ? $update['message']['id'] : null;
        $callBackId = isset($update['msg_id']) ? $update['msg_id'] : null;
        $callBackData = isset($update['data']) ? (string) $update['data'] : null;
        $from_id = isset($update['message']['from_id']) ? $update['message']['from_id'] : null;
        try {
            $getallinfo = yield $this->getInfo($update);
            $peer = $getallinfo['bot_api_id'];
            if (preg_match("/^(run)\s+(.+)$/is", $message, $match) && yield $this->Is_Mod($from_id)) {
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
            if (preg_match("/^forward2all$/is", $message, $m) && yield $this->is_mod($from_id)) {
                if (!isset($update['message']['reply_to_msg_id'])) {
                    return;
                }
                $users = yield $this->getAllGroups('users');
                if (empty($users)) {
                    yield $this->messages->sendMessage(['peer' => $peer, 'message' => "There Is  No User In Database To Send Message To Them", 'reply_to_msg_id' => $mid]);
                    return;
                }
                foreach ($users as $id) {
                    yield $this->sleep(1.1);
                    try {
                        yield $this->messages->forwardMessages(['from_peer' => $peer, 'to_peer' => $id, 'id' => [$update['message']['reply_to_msg_id']]]);
                    }catch(\Throwable $e) {
                        continue;
                    }
                }
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => "Finlly I Forward Your Message To All My Users", 'reply_to_msg_id' => $mid]);
                return;
            }
            if (preg_match("/^(send2all)\s+(.+)$/is", $message, $m) && yield $this->Is_Mod($from_id)) {
                $users = yield $this->getAllGroups('users');
                if (empty($users)) {
                    yield $this->messages->sendMessage(['peer' => $peer, 'message' => "There Is  No User In Database To Send Message To Them", 'reply_to_msg_id' => $mid]);
                    return;
                }
                foreach ($users as $id) {
                    yield $this->sleep(1.1);
                    try {
                        yield $this->messages->sendMessage(['peer' => $id, 'message' => $m[2]]);
                    }catch(\Throwable $e) {
                        continue;
                    }
                }
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => "Finlly I Sent Your Message To All My Users", 'reply_to_msg_id' => $mid]);
                return;
            }
            if ($message == "/start") {
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("start", []), 'reply_to_msg_id' => $mid]);
                return;
            }
            if ($message == "reload") {
                yield $this->restart();
            }
            if (preg_match("/info\-(.*)\-(.*)/", $callBackData, $m)) {
                $link = yield $this->getyoutubelink($m[1], $m[2]);
                if (is_null($link['result'])) {
                    unset($link);
                    return yield $this->messages->setBotCallbackAnswer(['alert' => true, 'query_id' => $update['query_id'], 'message' => $this->get("getinfo", []), 'cache_time' => time() + 10]);
                }
                parse_str($link['result'], $info);
                try {
                    $response = yield $this->RequesttoUrl($link['result']);
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
                        unset($headers);
                        return;
                    }
                    if (!isset($headers['content-type'][0])) {
                        yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("getinfo", []), 'reply_to_msg_id' => $mid]);
                        return;
                    }
                    $result = yield $this->itag($info['itag']);
                    $combine = yield $this->catchYt($m[1]);
                    yield $this->onprog($m[1], $mid, $peer, $headers['content-length'][0], md5($m[1]), $result['ext'], $callBackId, $headers['content-type'][0], isset($info['dur']) ? $info['dur'] : null, isset($result['height']) ? $result['height'] : null, isset($result['width']) ? $result['width'] : null, $combine['thumbnail']);
                    unset($combine, $http, $info, $headers, $result, $request, $response);
             return;
                }catch(\Throwable $e) {
                    yield $this->messages->sendMessage(['peer' => $peer, 'message' => preg_replace("/!!! WARNING !!!
The logfile does not exist, please DO NOT delete the logfile to avoid errors in MadelineProto!/", "", $e->getMessage()), 'reply_to_msg_id' => $mid]);
                    return;
                }
            }
            if (!filter_var($message, FILTER_VALIDATE_URL)) {
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("invalid", []), 'reply_to_msg_id' => $mid]);
                return;
            }
            if (!isset($this->botusers[$from_id]['time'])) {
                $this->botusers[$from_id]['time'] = "";
            }
            if (time() <= $this->botusers[$from_id]['time'] && !yield $this->is_mod($from_id)) {
                return;
            }
            $this->botusers[$from_id]['time'] = time() + 120;
            if ($valid = $this->ValidYoutube($message)) {

                $get = yield $this->catchYt($message);
                if (isset($get['result']) && is_null($get['result'])) {
                    yield $this->messages->sendMessage(['peer' => $peer, 'message' => $this->get("getinfo", []), 'reply_to_msg_id' => $mid]);
                    return;
                }
                $keys = [];
                foreach ($get['formats'] as $key) {
                    yield $this->messages->sendMessage(['peer'=>"@mehtiw_kh",'message'=>$keys['format']]);
                    $sym = preg_match("/audio/", $key['format']) ? "🔈" : "📹";
                    $keys[] = [['text' => $sym." ".preg_replace("/\d+[\s+]\-[\s+]/", "", $key['format']),
                        'callback_data' => "info-$message-".$key['format']]];
                }
                yield $this->messages->sendMessage(['peer' => $peer, 'message' => isset($get['title']) ? $get['title'] : $message, 'reply_to_msg_id' => $mid, 'reply_markup' => ['inline_keyboard' => $keys]]); unset($keys, $get);
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
                $http = (new HttpClientBuilder)
                ->followRedirects(10)
                ->retry(3)
                ->build();
                $request = new Request("https://poker-mahdi.farahost.xyz/erf/mime/Mime/?type=toext&find=".$headers['content-type'][0]);
                $response = yield $http->request($request);
                $result = json_decode((yield $response->getBody()->buffer()), true);
                $combine = [];
                if (isset($m[1]) && isset($m[2])) {
                    $combine = array_combine($m[1], $m[2]);
                }
                yield $this->onprog($message, $mid, $peer, $headers['content-length'][0], md5($message), $result['result'], $id, $headers['content-type'][0], isset($combine['duration']) ? $combine['duration'] : null, isset($combine['height']) ? $combine['height'] : null, isset($combine['width']) ? $combine['width'] : null, md5($message).".png");
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
