<?php
set_time_limit(0);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300); 
define('admin',1314349655);//ایدی ادمین رو جایوidadmin بزارید
define('API_KEY','1310596993:AAFH3knRJE1wuUx1Hg1Uh_rnxCJYATisHGs');//و اینجا بجای token توکن رباتتو قرار بده

if(!file_exists("manager.json")){
    touch("manager.json");
    $array = [];
    $array['gp'] = "";
    $array['spam'] = "off";
    $array['step'] = "none";
    $array['last'] = "متن پیشفرض";
    $array['foshlist'] = []; 
    $array['bots'] = [];   file_put_contents("manager.json",json_encode($array));
}
if(@filesize("error_log") >= 1 * 1024 * 1024){
    unlink("error_log");
}
function SendReq($method,$parameters=[]){
    $url = 'https://api.telegram.org/bot'.API_KEY.'/'.$method.'?';
    $ch = curl_init();
    curl_setopt($ch , CURLOPT_URL , $url);
    curl_setopt( $ch , CURLOPT_POST , true);
    curl_setopt( $ch , CURLOPT_POSTFIELDS, $parameters);
    curl_setopt( $ch , CURLOPT_RETURNTRANSFER , true);
    return json_decode(curl_exec($ch),true);
    curl_close($ch);
}
function deleteFolder($path){
  if(function_exists('exec')){
    exec("rm -rf $path");
    return !(is_file($path) || is_dir($path));
  }else{
    error_reporting(-1);
    if(rmdir($path) || unlink($path))
      return true;
    else{
      $glb = glob($path.'/*');
      foreach($glb as $value){
        if(is_dir($value))
          deleteFolder($value);
        else if(is_file($value))
          unlink($value);
      }
      return rmdir($path);
    }
  }
}
try{
    $update = json_decode(file_get_contents('php://input'),true);
    $inlineQuery = isset($update['inline_query']['query']) ? $update['inline_query']['query'] : null;
$inlineQueryId = isset($update['inline_query']['id']) ? $update['inline_query']['id'] : null;
$inlineQueryFromId = isset($update['inline_query']['from']['id']) ? $update['inline_query']['from']['id'] : null;
    $msgss = isset($update['message']) ? $update['message'] : null;
    $from_id = isset($msgss['from']['id']) ? $msgss['from']['id'] : null;
    $chat_id = isset($msgss['chat']['id']) ? $msgss['chat']['id'] : null;
    $mid = isset($msgss['message_id']) ? $msgss['message_id'] : null;
    $message = isset($msgss['text']) ? $msgss['text'] : null;
    $callBackData = isset($update['callback_query']['data']) ? $update['callback_query']['data'] : null;
$callBackId = isset($update['callback_query']['id']) ? $update['callback_query']['id'] : null;
$callBackFromId = isset($update['callback_query']['from']['id']) ? $update['callback_query']['from']['id'] : null;
$callBackChatId = isset($update['callback_query']['chat']['id']) ? $update['callback_query']['chat']['id'] : null;
$callBackMsgId = isset($update['callback_query']['inline_message_id']) ? $update['callback_query']['inline_message_id'] : null;
$callBackMsgId2 = isset($update['callback_query']['message']['message_id']) ? $update['callback_query']['message']['message_id'] : null;
$from_id = !is_null($callBackFromId) ? $callBackFromId : $from_id;
$manager = json_decode(file_get_contents("manager.json"),true);
if($from_id == admin){    
    if(preg_match("/^setgp (.*)/",$message,$m)){
       $manager['gp'] = $m[1];       file_put_contents("manager.json",json_encode($manager)); SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"حله ایدی گپ تارگت با موفقیت ثبت شد\nحالااگه میخوای اسپم رو شروع کنم فقط کافیه با دستور \nspam on این قابلیت رو فعال کنی",'reply_to_message_id'=>$mid]);
        return;
    }
    
    if($message == "/start"){  
    $txt = "راهنمای ربات😰\n•".$manager['gp']."\n•".str_replace(['on','off'],['فعال','غیرفعال'],$manager['spam'])."\n•setgp id\nتنظیم ایدی گروه تارگت\n•spam on|off\nفعال یا غیرفعال کردن حالت اسپم\n•addfosh text\nاضافه کردن فحش جدید به ربات\n•delfosh text\nحذف فحش مورد نظر از دیتابیس\n•foshlist\nمشاهده لیست فحش\n•newbot token\nساخت ربات جدید\n•left\nخروج ربات از گروه ثبت شده\n•botlist\nمشاهده لیست تمامی ربات های ساخته شده\n•settext text\nتنظیم پیام پیشفرض\n•deltext\nحذف پیام پیشفرض\nclean foshlist\nپاکسازی لیست فحش";
    SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>$txt,'reply_to_message_id'=>$mid]);
        return; 
    }
    if ($message == "deltext") {
            $manager['last'] = "";
            file_put_contents("manager.json",json_encode($manager));
            SendReq('sendMessage', ['chat_id' => $chat_id, 'text' => "متن پیشفرض با موفقیت حذف شد"]);
            return;
        }
    if(preg_match("/^spam (on|off)/",$message,$m)){
        $manager['spam'] = $m[1];
        file_put_contents("manager.json",json_encode($manager));
        SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"حالت اسپم با موفقیت ".str_replace(['on','off'],['فعال','غیرفعال'],$m[1])." شد",'reply_to_message_id'=>$mid]);
        return;
        }    
        if(preg_match("/^settext\s+(.+)/is",$message,$m)){
        $manager['last'] = $m[1];
        file_put_contents("manager.json",json_encode($manager,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"متن پیشفرض با موفقیت ثبت شد:))",'reply_to_message_id'=>$mid]);
        return;
        } 
        if($message == "clean foshlist"){
            $manager['foshlist'] = [];
            file_put_contents("manager.json",json_encode($manager,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"لیست فحش با موفقیت پاک شد",'reply_to_message_id'=>$mid]);
            return;
        }
    if(preg_match("/^addfosh\s+(.+)/is",$message,$m)){
        if(in_array($m[1],$manager['foshlist'])){
            SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"خطا\nاین فحش از قبل وجود داشته است",'reply_to_message_id'=>$mid]);
            return;
        }
        $manager['foshlist'][] = $m[1];
        file_put_contents("manager.json",json_encode($manager,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"فحش جدید با موفقیت اضافه شد\n$m[1]",'reply_to_message_id'=>$mid]);
        return;
        }
    if(preg_match("/^delfosh\s+(.+)/is",$message,$m)){
        if(!in_array($m[1],$manager['foshlist'])){
            SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"خطا\nاین فحش در دیتابیس ذخیره نشده است",'reply_to_message_id'=>$mid]);
            return;
        }        unset($manager['foshlist'][array_search($m[1],$manager['foshlist'])]);        file_put_contents("manager.json",json_encode($manager));        SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"$m[1] \nبا موفقیت از لیست فحش حذف شد",'reply_to_message_id'=>$mid]);
        return;
        }
        if($message == "foshlist"){
            if(empty($manager['foshlist'])){
                SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"لیست فحش خالی میباشد",'reply_to_message_id'=>$mid,'parse_mode'=>"MarkDown"]); 
                return;
            }
            $list = "";
            foreach($manager['foshlist'] as $f){
                static $id = 1;
                if($id == 15){
                    break;
                }
                $list .= $id." : `".$f."`\n";
                $id++;
            }           SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>$list,'reply_to_message_id'=>$mid,'parse_mode'=>"MarkDown"]); 
           return;
        }
        if($message == "left"){
        	if(empty($manager['gp'])){
        		SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"گروهی ثبت نشده است",'reply_to_message_id'=>$mid]);
                return;
        	}
        	SendReq('leaveChat',['chat_id'=>$manager['gp']]);
        	SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"حله با موفقیت از گروه لفت دادم",'reply_to_message_id'=>$mid]);
                return;
        }
        if(preg_match("/^newbot (.*)/",$message,$m)){
            if(in_array($m[1],array_keys($manager['bots']))){
                SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"مثل اینکه این توکن از قبل ست شده\nمشخصاتشو پایین برات میزارم\nاسم پوشه : ".$manager['bots'][$m[1]]['folder']."\nادرس کامل : ".$manager['bots'][$m[1]]['web'],'reply_to_message_id'=>$mid]);
                return;
            }
            
            $id = uniqid(time());
            mkdir("bots/".$id,0755);
            chmod("bots",0755);
            copy("bots/copySpam.php","bots/$id/spam.php");
            copy("bots/copyHarimSpamBot.php","bots/$id/HarimSpamBot.php");
            $file = file_get_contents("bots/$id/HarimSpamBot.php");
            $file = str_replace(["*ADMIN*","*TOKEN*"],[$from_id,$m[1]],$file);
            file_put_contents("bots/$id/HarimSpamBot.php",$file);
            $res=json_decode(file_get_contents("https://api.telegram.org/bot$m[1]/SetWebHook?url=".str_replace("HarimSpamBot.php","bots/$id/HarimSpamBot.php",$_SERVER['SCRIPT_URI'])),true);
     $manager['bots'][$m[1]]['folder'] = $id;
            $manager['bots'][$m[1]]['web'] = str_replace("HarimSpamBot.php","bots/$id/HarimSpamBot.php",$_SERVER['SCRIPT_URI']);
            file_put_contents("manager.json",json_encode($manager));        SendReq('sendMessage',['chat_id'=>admin,'text'=>json_encode($res)]); SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"خب رباتتو ساختم\nاسم پوشه ربات : `$id`\nتوکن ربات : `$m[1]`\nپیام زیر رو نگاه کن اگه\n`Webhook Was Set`\nیا\n`Webhook Already Set`\nبود یعنی رباتت با موفقیت ساخته شده😃🌹\n".$res['description']."\nخب این هم ادرس فایل برای کرون جاب :\n".str_replace("HarimSpamBot.php","bots/$id/spam.php",$_SERVER['SCRIPT_URI']),'reply_to_message_id'=>$mid,'parse_mode'=>"MarkDown"]);
          return;
        }
        if($message == "botlist"){
            if(empty($manager['bots'])){
                SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"شما تا بحال رباتی را ثبت نکرده اید",'reply_to_message_id'=>$mid]);
                return;
            }
            $list = "";
            foreach(array_keys($manager['bots']) as $names){
                static $i = 1;
                $list .= "•".$i ." - `".$names."` :\n•اسم فولدر : \n`".$manager['bots'][$names]['folder']."`\n•ادرس کامل :\n`".$manager['bots'][$names]['web']."`\n•برای حذف فایل کد پایین را کپی کرده و برای ربات بفرستید :\n`del_".$manager['bots'][$names]['folder']."`\n~~~~~~~~~~~~~\n";
                $i++;
            }
          SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>$list,'reply_to_message_id'=>$mid,'parse_mode'=>"MarkDown"]);  
          return;
        }
        if(preg_match("/^del\_(.*)/",$message,$m)){
           if(!file_exists("bots/".$m[1])){
               SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"اوه😨\nمثل اینکه این ربات|فولدر وجود نداره",'reply_to_message_id'=>$mid,'parse_mode'=>"MarkDown"]);  
               return;
           }
           deleteFolder("bots/".$m[1]);
          foreach(array_keys($manager['bots']) as $names){
              if($manager['bots'][$names]['folder'] == $m[1]){
         unset($manager['bots'][$names]);
                  file_put_contents("manager.json",json_encode($manager));     
                  break;
              }
          } SendReq('sendMessage',['chat_id'=>$chat_id,'text'=>"حله با موفقیت حذف شد",'reply_to_message_id'=>$mid,'parse_mode'=>"MarkDown"]);  
           return;
        }
    }    
    if($manager['spam'] == "on"){
       if(empty($manager['foshlist']))
          {
          return;
          }      SendReq('sendMessage',['chat_id'=>$manager['gp'],'text'=>$manager['last']."\n".$manager['foshlist'][array_rand($manager['foshlist'])],'parse_mode'=>"MarkDown"]);
          return;
}
}catch(Exception $e){
      SendReq('sendMessage',['chat_id'=>1314349655,'text'=>$e->getMessage()."\n".$e->getLine()]);
}catch(PDOException $e){
    SendReq('sendMessage',['chat_id'=>1314349655,'text'=>$e->getMessage()."\n".$e->getLine()]);
}