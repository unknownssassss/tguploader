<?php
ini_set('memory_limit', -1);
ini_set('max_execution_time', -1);
date_default_timezone_set("Asia/Tehran");
if (!\file_exists('madeline.php')) {
        \copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
    }
require_once('madeline.php');
use danog\MadelineProto\API;
use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\HttpException;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Exception;
use danog\MadelineProto\Logger;
use danog\MadelineProto\RPCErrorException;
class MrPoKeR extends EventHandler
{
    public function onStart(){
        			if(!\file_exists("manager.json")){
	        yield Amp\File\touch("manager.json");
	        $array['time'] = time();
         $array['run'] = "off";
         $array['flood'] = "off";
         $array['floodtime'] = 0;
         $array['next'] = "on";         
         $array['stoped'] = [];
         $array['bots'] = [];
         $array['urls'] = [];
         $array['messages'] = [];
         $array['messages'][] = "🤖 Message bots";
         $array['messages'][] = "🖥 Visit sites";
         $array['messages'][] = "📣 Join chats";
         $array['channels'] = [];
         yield Amp\File\put("manager.json",json_encode($array));
        }
    }
    private function getLocation($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $html = curl_exec($ch);
        $redirectURL = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        unset($ch,$html);
        return $redirectURL ? $redirectURL : $url;
    }
    private function openurl($link){
        $client = HttpClientBuilder::buildDefault();
                $request = new Request($link);
                $request->setBodySizeLimit(25 * 1024 * 1024); // 2 GB
                $request->setTransferTimeout(20 * 1000); // 2000 seconds
                $promise = $client->request($request);
                $response = yield$promise;
                yield $this->sleep(20);
                unset($promise,$response,$request,$client);
    }
    private function parselink($url){
        //https://dogeclick.com/join/M7B6Sh
        if(preg_match("/[https|http]+?\:\/\/[a-zA-Z0-9\_\-]+\.[\w\d]+\/([\w\d]+)/",$url,$m)){
          if(in_array($m[1],['join','bot'])) {
              return $this->getLocation($url);
          }
          return $url; 
        }
    }
	private $admin=array(1314349655);
	private $botid = 715510199;
	public function Is_Mod($id){
		if(!in_array($id,$this->admin)){
			return false;
			}
		return true;
		}
	 			public function nums($n){
		    			    $m = $n;
				while($m >= 1){
					yield $m;
					$m--;
					}
				}
    public function onUpdateNewChannelMessage($update)
    {
        yield $this->onUpdateNewMessage($update);
    }
    private function createmanager(){
        if(!\file_exists("manager.json")){
	        yield Amp\File\touch("manager.json");
	        $array['time'] = time();
         $array['run'] = "off";
         $array['flood'] = "off";
         $array['floodtime'] = 0;
         $array['next'] = "on";         
         $array['stoped'] = [];
         $array['bots'] = [];
         $array['urls'] = [];
         $array['messages'] = [];
         $array['messages'][] = "🤖 Message bots";
         $array['messages'][] = "🖥 Visit sites";
         $array['messages'][] = "📣 Join chats";
         $array['channels'] = [];
         yield Amp\File\put("manager.json",json_encode($array));
        }
    }
		public function getReportPeers(){
			return ['zbzfuabsgsy737272hdhs'];
			}
    final public function filePutContents (string $fileName, string $contents): Amp\Promise{
return Amp\File\put($fileName, $contents);
}  
private function getContents(string $path)
    {
        if(yield Amp\File\exists($path)){
           return Amp\File\get($path); 
        }
        yield $this->createmanager();
        return Amp\File\get($path); 
    }
    public function save($filename,array $data){
		$file = yield Amp\File\open($filename,"w");
		yield $file->write(json_encode($data));
		yield $file->close();
		unset($file);
		}
		private function count(array $array){
			$counter=0;
foreach($array as $i){
					$counter++;
				}
			return $counter;
			unset($counter);
			}
public function onUpdateNewMessage($update)
    {  
    	if (isset($update['message']) && $update['message']['out'] ?? false) {
            return;
        }
                   if ($update['message']['date'] < time() - 60) {
           return;     
        }   $message=isset($update['message']['message']) ? $update['message']['message'] : null;
  	 $mid=isset($update['message']['id']) ? $update['message']['id'] : null;
  	$from_id=isset($update['message']['from_id']) ? $update['message']['from_id'] : null;
 	 	 try{
     //start
     $manager = json_decode(yield $this->getContents('manager.json'),true);
		$get_info = yield $this->getInfo($update);
	    $peer = $get_info['bot_api_id'];
	    if($manager['flood'] == "on" && $manager['floodtime'] <= time()){
	        $manager['run'] = "on";
	        $manager['flood'] = "off";
	        $manager['floodtime'] = 0;
	        yield $this->save("manager.json",$manager);
	    }
	    if(!empty($manager['stoped'])){
	        foreach($manager['stoped'] as $key=>$value){
	            if($value <= time()){
	                unset($manager['stoped'][$key]);
	                yield $this->save("manager.json",$manager);
	            }
	        }
	    }
	    if(!empty($manager['channels'])){
	        foreach($manager['channels'] as $key=>$value){
	            if($value <= time()){
	              try{
	                  unset($manager['channels'][$key]);
	                yield $this->save("manager.json",$manager); 
	    	      yield $this->channels->leaveChannel(['channel' => $key]); 
	    	   }  catch(\Throwable $e){
	    	       unset($e);
	    	       continue;
	    	   } 
	            }
	        }
	    }
	    /*if($manager['next'] <= time()){
	        $manager['next'] = time() + 30;
	        yield $this->save('manager.json',$manager);
	        $gethistory = yield $this->messages->getHistory
(['peer' => 1228563443, 'offset_id' => 0,'offset_date' => 0, 'add_offset' => 0,
'limit' => 1,
'max_id' => 0, 'min_id' => 0, 'hash' => 0])['messages'][0]; 
if(isset($gethistory['reply_markup'])){
	                if(isset($gethistory['reply_markup']['rows'][0]['buttons'][0])){
	         yield $gethistory['reply_markup']['rows'][0]['buttons'][0]->click(true); 
	         return;         
	                    }
	                    }   	        
	    }*/
	    if($message == "getusage" && yield $this->Is_Mod($from_id)){
	        yield $this->messages->sendMessage(['peer'=>$peer,'message'=>"Ram Usage\n".memory_get_usage() / 1024 / 1024,'reply_to_msg_id'=>$mid]);
	        return;
	    }
	    if($message == "ping" && yield $this->Is_Mod($peer)){
	        yield $this->messages->sendMessage(['peer'=>$peer,'message'=>"Pong",'reply_to_msg_id'=>$mid]);
	        return;
	    }
	    if($message == "my coin" && yield $this->is_mod($peer)){
	        yield $this->messages->sendMessage(['peer'=>$this->botid,'message'=>"💰 Balance"]);
	        yield $this->sleep(1.5);
	        $gethistory = yield $this->messages->getHistory
(['peer' => $this->botid, 'offset_id' => 0,'offset_date' => 0, 'add_offset' => 0,
'limit' => 1,
'max_id' => 0, 'min_id' => 0, 'hash' => 0])['messages'][0]; 
yield $this->messages->sendMessage(['peer'=>$peer,'message'=>$gethistory['message']]);
unset($gethistory);
return;
	    }
	    if(preg_match("/^mine (on|off)/i",$message,$m) && yield $this->Is_Mod($from_id)){
	     $manager['run'] = $m[1];
	     yield $this->save("manager.json",$manager); 
	     yield $this->messages->sendMessage(['peer'=>$peer,'message'=>"Auto Mining Turned ".$m[1],'reply_to_msg_id'=>$mid]);
	    }
	    if($message == "restart" && yield $this->Is_Mod($from_id)){
	        yield $this->messages->sendMessage(['peer'=>$peer,'message'=>"Restarted"]);
	       yield $this->restart(); 
	       return;       
	    }
	    		if(preg_match("/^(run)\s+(.+)$/is",$message,$match) && yield $this->is_mod($from_id)){
                         try{
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
                            yield $this->messages->sendMessage(['peer'=>$peer,'message'=>"Code : \n`".$match[2]."`\nResult : \n".strip_tags($run)."\n"]);
                          unset($run);
                          return;
					}
	    
	    if($manager['run'] == "on" && $manager['time'] <= time()){
	        $rand = $manager['messages'];
	        $rand = $rand[array_rand($rand)]; 
	        	        if(in_array($rand,array_keys($manager['stoped']))){
	            return;
	        }
	        $manager['time'] = time() + 20;
	        yield $this->save("manager.json",$manager);
	    $ids = yield $this->messages->sendMessage(['peer'=>$this->botid,'message'=>$rand]);
	    if (!isset($ids['id'])) {
                foreach ($ids['updates'] as $updat) {
                    if (isset($updat['id'])) {
                        $ids = $updat['id'];
                        break;
                    }
                }
            } else {
                $ids = $ids['id'];
            }
	     yield $this->sleep(1); 
	        $gethistory = yield $this->messages->getMessages(['peer' => $this->botid,'id'=>[$ids+1]])['messages'][0];    
if(preg_match("/there are no new ads available/is",$gethistory['message'] ?? "")){
	            $manager['stoped'][$rand] = strtotime("20 min",time());
	            yield $this->save("manager.json",$manager);
	            return;
	            }	            if(isset($gethistory['reply_markup'])){
	                if(isset($gethistory['reply_markup']['rows'][0]['buttons'][0]['url'])){
	                $url = $this->parselink(str_replace("doge.click","dogeclick.com",$gethistory['reply_markup']['rows'][0]['buttons'][0]['url']));
	               // yield $this->messages->sendMessage(['peer'=>$this->admin[0],'message'=>$url]);
	            if(preg_match("/[https|http]+\:\/\/[t\.me|telegram\.me]+\/([a-zA-Z0-9\_]+)(\?start\=[a-zA-Z0-9\_\-]+)?/",$url,$m) && $rand == "🤖 Message bots"){
	            yield $this->messages->startBot(['bot' =>$m[1],'start_param' => 'start']);  
	          yield $this->sleep(2);
	          $msgid = yield $this->messages->getHistory
(['peer' => $m[1], 'offset_id' => 0,'offset_date' => 0, 'add_offset' => 0,
'limit' => 1,
'max_id' => 0, 'min_id' => 0, 'hash' => 0])['messages'][0]['id'];
	          yield $this->messages->forwardMessages([
    'from_peer'=>$m[1],
    'to_peer'=>$this->botid,
    'id'=>[$msgid]
  ]);
  yield $this->sleep(3);
  yield $this->messages->deleteHistory([
  'revoke' => true,
  'peer' => $m[1], 'max_id' => $mid
 ]);
 if(isset($gethistory['reply_markup']['rows'][1]['buttons'][1])){
	 yield $gethistory['reply_markup']['rows'][1]['buttons'][1]->click(true);
}
  return;     
	                }
	        if($rand == "🖥 Visit sites"){
	            yield $this->openurl($url);
	            yield $this->sleep(1);       if(isset($gethistory['reply_markup']['rows'][1]['buttons'][1])){
	 yield $gethistory['reply_markup']['rows'][1]['buttons'][1]->click(true);
}
	            return;
	        }
	       if($rand == "📣 Join chats"){	           if(isset($gethistory['reply_markup']['rows'][0]['buttons'][1])){
	           try{
	           yield $this->channels->joinChannel(['channel' =>$url]);
	 yield $gethistory['reply_markup']['rows'][0]['buttons'][1]->click(true); 
	 yield $this->sleep(2); 
	 $msgid = yield $this->messages->getHistory
(['peer' => $url, 'offset_id' => 0,'offset_date' => 0, 'add_offset' => 0,
'limit' => 1,
'max_id' => 0, 'min_id' => 0, 'hash' => 0])['messages'][0]['message'];
if(preg_match("/least ([0-9]+) ([a-zA-Z]+)/",$msgid,$s)){
  $manager['channels'][$url] = strtotime("+$s[1] $s[2]",time());  
}else{
    $manager['channels'][$url] = strtotime("+2 hour",time());
}
	   yield $this->save("manager.json",$manager);
	    if(isset($gethistory['reply_markup']['rows'][1]['buttons'][1])){
	 yield $gethistory['reply_markup']['rows'][1]['buttons'][1]->click(true);
	        }
	   return;
	   }catch(\Throwable $e){
	   if(isset($gethistory['reply_markup']['rows'][1]['buttons'][1])){
	 yield $gethistory['reply_markup']['rows'][1]['buttons'][1]->click(true);
}  

yield $this->report($e->getMessage()."\n".$e->getLine()); 
	   }
	          }          
	           return;
	       }                            
	                }
	            }            
	        }	    	    
	     
	     if($peer == $this->botid){
	         if(isset($message)){
	             if($message == "There is a new site for you to visit! 🖥"){
	                 unset($manager['stoped']['🖥 Visit sites']);
	                 yield $this->save("manager.json",$manager);
	                 return;
	             }
	             if($message == "There is a new chat for you to join! 📣"){
	                unset($manager['stoped']['📣 Join chats']);
	                 yield $this->save("manager.json",$manager);	                 
	                 return; 
	             }
	             if($message == "There is a new bot for you to message! 🤖"){
	                unset($manager['stoped']['🤖 Message bots']);
	                 yield $this->save("manager.json",$manager);
	                 
	                 return;  
	             }
	         }
	     } 
	        
	 } catch(\Throwable $e){
	    					if(preg_match("/FLOOD\_WAIT\_([0-9]+){1,6}/",$e->getMessage(),$m)){
	     					   $manager['run'] = "off";
	     					   $manager['flood'] = "on";
	     					   $manager['floodtime'] = time() + $m[1];
	     					   yield $this->save("manager.json",$manager);
	     					    }     
		   yield $this->report("➲Error :".$e->getMessage()."\n".$e->getLine()."\n".$e->getFile());
    }
    }
}

$settings=[];
$settings['serialization']['serialization_interval'] = 60 * 6;
$settings['logger']['logger_level'] = 5;
$settings['logger']['logger'] = \danog\MadelineProto\Logger::FILE_LOGGER;
$settings['logger']['max_size'] = 2 * 1024 *1024;
$settings['peer']['cache_all_peers_on_startup'] = true;
$settings['serialization']['cleanup_before_serialization']=true;
$mProto = new API("ltc.madeline",$settings);
$mProto->startAndLoop(MrPoKeR::class);
