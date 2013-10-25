<?php
function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}
function get_string_between($string, $start, $end){
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
}


$files = array(
'/home/starmade/StarMadeServer/StarMade/logs/log.txt.0',
'/home/starmade/StarMadeServer/StarMade/logs/log.txt.1',
'/home/starmade/StarMadeServer/StarMade/logs/log.txt.2',
'/home/starmade/StarMadeServer/StarMade/logs/log.txt.3',
'/home/starmade/StarMadeServer/StarMade/logs/log.txt.4',
'/home/starmade/StarMadeServer/StarMade/logs/log.txt.5',
'/home/starmade/StarMadeServer/StarMade/logs/log.txt.6',
'/home/starmade/StarMadeServer/StarMade/logs/log.txt.7',
'/home/starmade/StarMadeServer/StarMade/logs/log.txt.8',
'/home/starmade/StarMadeServer/StarMade/logs/log.txt.9',
'/home/starmade/StarMadeServer/StarMade/logs/log.txt.10'
);
$chat = array();
$strsmilie = array(':D',';)',':)',':P',':(','xD','XD','xd','X)',':DD');
$imgsmilie = array('<img src="smilie/s1.png">','<img src="smilie/s2.png">','<img src="smilie/s3.png">','<img src="smilie/s4.png">','<img src="smilie/s5.png">','<img src="smilie/s6.png">','<img src="smilie/s1.png">');

$bufferline = ""; 

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
    		}
			fclose($handle);
		}
	}
}

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

echo $output;
?>