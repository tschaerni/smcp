<?php
$basedir = '/var/www/feedupyourbeast.de/chat/';

include_once($basedir."inc/func.php");
include_once($basedir.'inc/config.php');

if ($status == "off") {
	exit();
}

$fd = $filedepth;
$files = array();

for ($i = 0; $i <= $fd; $i++) {
	array_push($files, $starmadedir.'/StarMade/logs/log.txt.'.$i);
}

$players = array();
$chat = array();

$time = time();
$time_check = $time - $minoffset;

$bufferline = ""; 

$lines = array();
$i = 0;

foreach($files as $f) {
	if (file_exists($f)) {
		$handle = @fopen($f, "r");
		if ($handle) {
			while (($buffer = fgets($handle, 4096)) !== false) {
				// PM WISPER und FACTION Chat abfangen
				if (strpos($buffer,"[SERVER][CHAT][WISPER]")) {
					$getbuffer = str_replace(array('[SERVER][CHAT][WISPER]','[PM]'),array('',''),$buffer);
					
					$line = preg_split("/\[.*?\]/", $getbuffer);
					$gettime = preg_split("/\[\w+\]/", $buffer);
					
					preg_match_all("/\[.*?\]/",$getbuffer,$matches);
					
					$time = strtotime(str_replace(array('[',']'),array('',''),$gettime[0]));
					
					$from = str_replace(array('[',']'),array('',''),$matches[0][1]);
					$to = str_replace(":","",$line[1]);
					
					$nickname = '<b>'.$from." -> ".$to.'</b>';
					$chat_txt = $line[2];
					
					if ($from == "FACTION") {
						$chat_txt = $line[3];
					} else {
						$nickname = 'WISPER<br /><b>'.$from." -> ".trim($to).'</b>';
					}

					array_push($chat, array('time' => $time, 'nickname' => $nickname, 'chat' => $chat_txt));
				}
				
				// Öffentlichen Chat abfangen
				if (strpos($buffer,"[CHAT] Server(0)") && !strpos($buffer,"initializing data from network object")) {
					$line = preg_split("/\[\w+\]/", $buffer);
					$time = strtotime(str_replace(array('[',']'),array('',''),$line[0]));
										
					$split_txt = preg_split('/:\s*/', str_replace(array(' Server(0) '),array(''),$line[1]), 2);
					$nickname = $split_txt[0];
					$chat_txt = $split_txt[1];
										
					array_push($chat, array('time' => $time, 'nickname' => $nickname, 'chat' => $chat_txt));
				}
				
				// SERVER Messages abfangen
				if (strpos($buffer,"SERVERMSG (type 0)")) {
					$line = preg_split("/\[\w+\]/", $buffer);
					$time = strtotime(str_replace(array('[',']'),array('',''),$line[0]));
					
					if (isset($line[3])) {
						$getlinef = explode(']',str_replace(array('SERVERMSG (type 0): '),array(''),$line[3]));
					
						$getline = $getlinef[0];
					
						if ($getline != $bufferline) {
							$bufferline = $getline;
							if (!strpos($bufferline, "##########")) {
								array_push($chat, array('time' => $time, 'nickname' => "[SERVER]", 'chat' => $bufferline));
							}
						}
					}
				}
				// PM WISPER SERVER Chat abfangen
				if (strpos($buffer,"[SERVER-LOCAL-ADMIN] END; send to")) {
					preg_match_all("/\[.*?\]/",$buffer,$matches);
					$time = strtotime(str_replace(array('[',']'),array('',''),$matches[0][0]));
					
					$get_to_text = explode('|',str_replace(array($matches[0][0].' ','[SERVER-LOCAL-ADMIN] END; send to ',' as server message: '),array('','','|'),$buffer));
					
					$nickname = 'WISPER<br /><b><span class="server">[SERVER]</span> -> '.trim($get_to_text[0]).'</b>';
					$chat_txt = $get_to_text[1];
					
					array_push($chat, array('time' => $time, 'nickname' => $nickname, 'chat' => $chat_txt));
				}
							
				// Who is Online #1
				if (strpos($buffer,"[CONTROLLER][ADD-UNIT] (Server(0)): PlS")) {
					$line = preg_split("/\[\w+\]/", $buffer);
					$gettime = strtotime(str_replace(array('[',']'),array('',''),$line[0]));
										
					preg_match_all("/\[.*?\]/",$line[1],$matches);
					$getplayer = explode(";",$matches[0][1]);
					$player = str_replace(array('['),array(''),$getplayer[0]);
					
					if ($gettime > $time_check) {
						if (!searchMyArray($player, $players)) {  
 							array_push($players, array('time' => $gettime, 'player' => $player, 'sortplayer' => strtolower($player)));
						}
					}
				}
				// Who is Online #2
				if (strpos($buffer,"spike")) {
					$getpreline = $lines[($i - 1)];
					$line = preg_split("/\[\w+\]/", $getpreline);
					$gettime = strtotime(str_replace(array('[',']'),array('',''),$line[0]));
					
					$player = trim(str_replace(array('brace yourself for a lag spike] to RegisteredClient: ','connected: true','(',')'),array('','',''),preg_replace( "/\(.*\)/U","", $buffer)));
					
					if ($gettime > $time_check) {
						if (!searchMyArray($player, $players)) {  
 							array_push($players, array('time' => $gettime, 'player' => $player, 'sortplayer' => strtolower($player)));
						}
					}
				}
				array_push($lines, $buffer);
				$i++;
			}
			fclose($handle);
		}
	}
}

// Write players.log
array_sort_by_column($players, 'sortplayer', SORT_ASC);

$file = $log_dir.$player_log;

if (file_exists($file)) {
	unlink($file);
}

$handle = fopen($file,"a+");

foreach($players as $p) {
	if (!strpos($p['player'],"[SEND][SERVERMESSAGE] [SERVERMSG : prepare for lag spike ] to RegisteredClient") && !strpos($p['player'],"[SERVER-LOCAL-ADMIN]")) {
		fwrite($handle, $p['player']."\n");
	}
}
fclose($handle);

// Write chat.log
array_sort_by_column($chat, 'time', SORT_ASC);

$file = $log_dir.$chat_log;

if (file_exists($file)) {
	unlink($file);
}

$handle = fopen($file,"a+");

foreach($chat as $c) {
	$date = date("d.m.Y",$c['time'])." ".date("H:i:s",$c['time']);
	$line = $date.'|'.$c['nickname'].'|'.trim(str_replace("\n","",$c['chat']))."\n";

	fwrite($handle, $line);
}
fclose($handle);

?>