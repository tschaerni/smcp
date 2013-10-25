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
function searchMyArray( $needle, $haystack, $strict=false, $path=array() )
{
    if( !is_array($haystack) ) {
        return false;
    }
 
    foreach( $haystack as $key => $val ) {
        if( is_array($val) && $subPath = searchMyArray($needle, $val, $strict, $path) ) {
            $path = array_merge($path, array($key), $subPath);
            return $path;
        } elseif( (!$strict && $val == $needle) || ($strict && $val === $needle) ) {
            $path[] = $key;
            return $path;
        }
    }
    return false;
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
);
$players = array();


$time = time();
$time_check = $time - 330;

$bufferline = ""; 

$lines = array();
$i = 0;

foreach($files as $f) {
	if (file_exists($f)) {
		$handle = @fopen($f, "r");
		if ($handle) {
    		while (($buffer = fgets($handle, 4096)) !== false) {
    		
    			/*if (strpos($buffer,"[CHAT] Server(0)") && !strpos($buffer,"initializing data from network object")) {
    				$line = preg_split("/\[\w+\]/", $buffer);
    	    		$time = strtotime(str_replace(array('[',']'),array('',''),$line[0])) + 7200;
    	    		
    	    		$split_txt = explode(":", str_replace(array(' Server(0) '),array(''),$line[1]));
    	    		$player = trim($split_txt[0]);
    	    		
    	    		if ($time > $time_check) {
    					if (!searchMyArray($player, $players)) {  
    	    				array_push($players, array('time' => $time, 'player' => $player, 'sortplayer' => strtolower($player)));
    	    			}
    	    		}
    	    		//array_push($chat, array('time' => $time, 'nickname' => $split_txt[0], 'chat' => $split_txt[1]));
        		}
        		
        		//echo $buffer.'<br>';
    			*/
    			if (strpos($buffer,"[CONTROLLER][ADD-UNIT] (Server(0)): PlS")) {
					
    				$line = preg_split("/\[\w+\]/", $buffer);
    				$gettime = strtotime(str_replace(array('[',']'),array('',''),$line[0]));
    				
    				//echo $buffer."<br>";
    				//echo '<pre>';
    				//print_r($line);
    				
    				//echo $buffer;
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
    			
    			//echo $buffer.'<br>';
    			if (strpos($buffer,"spike")) {
    				//echo $lines[($i - 1)].'<br>';
    				//echo $buffer."<br>";
    				
    				$getpreline = $lines[($i - 1)];
    				
    				$line = preg_split("/\[\w+\]/", $getpreline);
    				$gettime = strtotime(str_replace(array('[',']'),array('',''),$line[0]));
    				
    				//echo $gettime;
    				$player = trim(str_replace(array('brace yourself for a lag spike] to RegisteredClient: ','connected: true','(',')'),array('','',''),preg_replace( "/\(.*\)/U","", $buffer)));
    				//$string = preg_replace( "/\(.*\)/U","", $string );  
    				//echo $player;
    				if ($gettime > $time_check) {
    					if (!searchMyArray($player, $players)) {  
 							array_push($players, array('time' => $gettime, 'player' => $player, 'sortplayer' => strtolower($player)));
						}
    				}
				}
    			//echo $buffer."<br>	";
    			array_push($lines, $buffer);
    			$i++;
    		}
			fclose($handle);
		}
	}
}

//exit();

array_sort_by_column($players, 'sortplayer', SORT_ASC);

$output = '';

$output .= '<ul>';
foreach($players as $p) {
	$output .= '<li>'.$p['player'].'<img src="foot.png" style="float: right; cursor: pointer;" height="16" data-nickname="'.$p['player'].'"></li>';
}
$output .= '</ul>';

header("Content-Type: text/html; charset=utf-8");

echo $output;
?>