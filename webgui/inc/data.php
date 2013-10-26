<?php
include_once("func.php");
include_once('config.php');

$fd = $filedepth;
$files = array();

for ($i = 0; $i <= $fd; $i++) {
	array_push($files, $starmadedir.'/logs/log.txt.'.$i);
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
    		while (($buffer = fgets($handle)) !== false) {
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
					$chat_txt = str_replace($strsmilie,$imgsmilie,$line[2]);
					
					if ($from == "FACTION") {
						$chat_txt = str_replace($strsmilie,$imgsmilie,$line[3]);
					} else {
						$nickname = 'WISPER<br /><b>'.$from." -> ".$to.'</b>';
					}

					array_push($chat, array('time' => $time, 'nickname' => $nickname, 'chat' => $chat_txt));
				}
				
				// Öffentlichen Chat abfangen
    			if (strpos($buffer,"[CHAT] Server(0)") && !strpos($buffer,"initializing data from network object")) {
    				$line = preg_split("/\[\w+\]/", $buffer);
    	    		$time = strtotime(str_replace(array('[',']'),array('',''),$line[0]));
    	    		
    	    		$split_txt = explode(":", str_replace(array(' Server(0) '),array(''),$line[1]));
    	    		    	    		
    	    		array_push($chat, array('time' => $time, 'nickname' => $split_txt[0], 'chat' => str_replace($strsmilie,$imgsmilie,$split_txt[1])));
        		}
        		
        		// SERVER Messages abfangen
        		if (strpos($buffer,"SERVERMSG (type 0)")) {
        			$line = preg_split("/\[\w+\]/", $buffer);
    	    		$time = strtotime(str_replace(array('[',']'),array('',''),$line[0]));
    	    		
    	    		$getline = str_replace(array('SERVERMSG (type 0): '),array(''),str_replace($strsmilie,$imgsmilie,get_string_between($line[2], "[", "]")));
    	    		
    	    		if ($getline != $bufferline) {
    	    			$bufferline = $getline;
    	    			if (substr($bufferline, 0, 4) != "####") {
    	    				array_push($chat, array('time' => $time, 'nickname' => "[SERVER]", 'chat' => $bufferline));
    	    			}
    	    		}
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

switch ($_GET['a']) {
	// Output Players for Who is Online
    case 'players':
		array_sort_by_column($players, 'sortplayer', SORT_ASC);
		
		$output = '';
		
		$output .= '<ul>';
		foreach($players as $p) {
			$output .= '<li>'.$p['player'].'<img src="img/foot.png" style="float: right; cursor: pointer;" height="16" data-nickname="'.$p['player'].'"></li>';
		}
		$output .= '</ul>';
		
		header("Content-Type: text/html; charset=utf-8");
		
		echo $output;
 	break;
 	// Output for LiveChat
	case 'chat':
		array_sort_by_column($chat, 'time', SORT_ASC);
			
		$output = '';
		
		$output .= '<table>';
		foreach($chat as $c) {
			$output .= '<tr>';
		
			$output .= '<td nowrap>';
			if ($c['nickname'] == "[SERVER]") {
				$output .= '<strong>'.$c['nickname'].'</strong>';
			} else {
				$output .= '<i>'.$c['nickname'].'</i>';
			}
			$output .= '<span>'.date("d.m.Y",$c['time'])." ".date("H:i:s",$c['time']).'</span>';
			$output .= '</td>';
			
			$output .= '<td>';
			$output .= $c['chat'];
			$output .= '</td>';
			
			$output .= '</tr>';
		}
		$output .= '</table>';

		header("Content-Type: text/html; charset=utf-8");
		
		if (!isset($index)) {
			echo $output;
		}
	break;
}

?>