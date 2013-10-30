<?php
include_once("func.php");
include_once('config.php');

header("Content-Type: text/html; charset=utf-8");

switch ($_GET['a']) {
	// Output Players for Who is Online
	case 'players':
	
		$file = $log_dir.$player_log;
		
		$output = '';
		$output .= '<ul>';
		
		if (file_exists($file)) {
			$handle = @fopen($file, "r");
			if ($handle) {
				while (($buffer = fgets($handle, 4096)) !== false) {
					$nickname = str_replace("\n","",$buffer);
					$output .= '<li>'.$nickname.'<img src="img/foot.png" class="kick" height="16" title="'.$nickname.' kicken" data-nickname="'.$nickname.'"><img src="img/pm.png" class="pm" height="14" title="'.$nickname.' eine PM schicken" data-nickname="'.$nickname.'"></li>';
				}
			}
		}
		
		$output .= '</ul>';
				
		echo $output;
		
 	break;
 	// Output for LiveChat
	case 'chat':
		$file = $log_dir.$chat_log;
		
		$output = '';
		$output .= '<table>';
		
		if (file_exists($file)) {
			$handle = @fopen($file, "r");
			if ($handle) {
				while (($buffer = fgets($handle, 4096)) !== false) {
					$items = explode("|",$buffer);
					
					$time = $items[0];
					$nickname = $items[1];
					$text = $items[2];
					
					$output .= '<tr>';
							
					$output .= '<td nowrap>';
					
					if ($nickname == "[SERVER]") {
						$output .= '<strong>'.$nickname.'</strong>';
					} else {
						$output .= '<i>'.$nickname.'</i>';
					}
					
					$output .= '<span>'.$time.'</span>';
					$output .= '</td>';
					
					$output .= '<td>';
					if (strpos($buffer,"http://")) {
						$output .= $text;
					} else {
						$output .= str_replace($strsmilie,$imgsmilie,$text);
					}
					$output .= '</td>';
					
					$output .= '</tr>';
				}
			}
		}
		
		$output .= '</table>';
		
		echo $output;
	break;
	case 'admins':	
		$output = shell_exec("sudo -u starmade ".$starmadedir."/functionlib.sh admins");
		$admins_active = explode("\n",str_replace(array("\r"),array(''),trim($output)));
		
		$output = '<table>';
		
		foreach($admins as $a) {
			$style = '';
			$active = '';
			if (in_array($a,$admins_active,true)) {
				$style = ' style="color: #cc0000 !important;"';
				$active = '<span>active</span>';
			}
			$output .= '<tr>';
			$output .= '<td><a href="#" class="'.$a.'"'.$style.'>'.$a.'</a>'.$active.'</td>';
			$output .= '</tr>';
		}
		$output .= '</table>';
				
		echo $output;
	break;
}

?>