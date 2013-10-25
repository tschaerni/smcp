<?php
function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=>$row) {
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
$strsmilie = array(':D',';)',':)',':P',':(','xD','XD','xd','X)');
$imgsmilie = array('<img src="/smilie/s1.png">','<img src="/smilie/s2.png">','<img src="/smilie/s3.png">','<img src="/smilie/s4.png">','<img src="/smilie/s5.png">','<img src="/smilie/s6.png">');

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
					
					$nickname = '<b>'.$from." ->".$to.'</b>';
					$chat_txt = str_replace($strsmilie,$imgsmilie,$line[2]);
					
					if ($from == "FACTION") {
						$chat_txt = str_replace($strsmilie,$imgsmilie,$line[3]);
					} else {
						$nickname = 'WISPER<br /><b>'.$from." ->".$to.'</b>';
					}

					array_push($chat, array('time' =>$time, 'nickname' =>$nickname, 'chat' =>$chat_txt));
				}
				
				// Öffentlichen Chat abfangen
    			if (strpos($buffer,"[CHAT] Server(0)") && !strpos($buffer,"initializing data from network object")) {
    				$line = preg_split("/\[\w+\]/", $buffer);
    	    		$time = strtotime(str_replace(array('[',']'),array('',''),$line[0]));
    	    		
    	    		$split_txt = explode(":", str_replace(array(' Server(0) '),array(''),str_replace($strsmilie,$imgsmilie,$line[1])));
    	    		    	    		
    	    		array_push($chat, array('time' =>$time, 'nickname' =>$split_txt[0], 'chat' =>$split_txt[1]));
        		}
        		
        		// SERVER Messages abfangen
        		if (strpos($buffer,"SERVERMSG (type 0)")) {
        			$line = preg_split("/\[\w+\]/", $buffer);
    	    		$time = strtotime(str_replace(array('[',']'),array('',''),$line[0]));
    	    		
    	    		$getline = str_replace(array('SERVERMSG (type 0): '),array(''),str_replace($strsmilie,$imgsmilie,get_string_between($line[2], "[", "]")));
    	    		if ($getline != $bufferline) {
    	    			$bufferline = $getline;
    	    			if (substr($bufferline, 0, 4) != "####") {
    	    				array_push($chat, array('time' =>$time, 'nickname' =>"[SERVER]", 'chat' =>$bufferline));
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

?>
<!doctype html>
<html>
<head>
<title>LiveChat - FeedUpYourBeast</title>
<style type="text/css">
body {
	margin: 0;
	padding: 0;
	background: #000;
	color: #e7e7e7;
	font-family: Arial, Tahoma;
	font-size: 14px;
	line-height: 17px;
}
#wrapper {
	width: 670px;
	margin: 0 auto;
}
#chat {
	display: block;
	width: 450px;
	height: 450px;
	padding: 20px;
	overflow: auto;
	margin: 20px 0 20px 0;
	border: 1px solid #333;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	text-align: left;
}
#chat table {
    padding: 0;
    border-collapse: collapse;
}
#chat td {
	padding: 4px 6px 4px 6px;
	vertical-align: middle;
	font-size: 12px;
}
#chat td strong {
	text-weight: normal;
	color: #CC0000;
}
#chat td b {
	font-size: 9px;
}
#chat td span {
	display: block;
	color: #666;
	font-size: 8px;
	line-height: normal;
}
#chat td img {
	margin-bottom: -4px;
}
#chat td:first-child {
	padding-right: 10px;
}
#form {
	width: 450px;
	margin: 0 0 0 24px;
}
#form input {
	border: 1px solid #fff;
	background: #232323;
	color: #fff;
	font-family: Arial, Tahoma;
	padding: 6px 30px 6px 6px;
	width: 90%;
}
h1 {
	width: 400px;
	margin: 20px 0 20px 0;
	padding-left: 24px;
	font-size: 22px;
	color: #666;
}
h2 {
	width: 180px;
	margin: 20px 0 0 0;
	padding: 0;
	padding-left: 24px;
	font-size: 16px;
	color: #666;
}
h2 span {
	font-size: 9px;
}
#players {
	display: block;
	width: 160px;
	height: 450px;
	padding: 20px;
	margin: 0 0 20px 0;
	border: 1px solid #333;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	text-align: left;
	overflow: auto;
}
#count {
	color: #666;
	text-align: left;
	font-size: 9px;
	margin: 0 0 0 24px;
}
#players .inner {
	
}
#players .inner ul, #players .inner ul li {
	padding: 0;
	margin: 0;
	list-style: none;
	line-height: 16px;
}
#players .inner ul li {
	padding: 4px;
	font-size: 11px;
}
#lastcmds {
	position: relative;
	display: block;
	width: 490px;
	height: 90px;
	margin: 20px 0 20px 0;
	border: 1px solid #333;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	text-align: left;
	font-size: 11px;
	color: #666;
	line-height: 18px;
}
#lastcmds img {
	position: absolute;
	top: 6px;
	right: 6px;
	cursor: pointer;
}
#lastcmds .inner {
	overflow: auto;
	padding: 10px 20px 20px 20px;
	height: 60px;
}
#addadmin {
	display: block;
	width: 160px;
	height: 120px;
	padding: 0 0 0 20px;
	margin: 10px 0 0 4px;
	text-align: left;
}
#addadmin ul, #paddadmin li {
	padding: 0;
	margin: 0;
	list-style: none;
	line-height: 15px;
}
#addadmin ul li {
	padding: 0;
	font-size: 11px;
}
#addadmin table {
    padding: 0;
    width: 95%;
    border-collapse: collapse;
}
#addadmin td {
	font-size: 11px;
	line-height: 15px;
	padding: 0;
}
#addadmin td span {
	float: right;
	color: #00cc00;
	font-size: 9px;
}
#addadmin a {
	color: #5e4949;
	text-decoration: none;
}
#addadmin a:hover {
	color: #cc0000;
	text-decoration: underline;
}
#commands {
	display: none;
	position: fixed;
	top: 50%;
	left: 50%;
	background: #000;
	width: 640px;
	height: 340px;
	
	margin-top: -170px;
	margin-left: -320px;

	border: 2px solid #222;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	-webkit-box-shadow:  0px 0px 30px 60px rgba(0, 0, 0, 0.7);
    -moz-box-shadow:  0px 0px 30px 60px rgba(0, 0, 0, 0.7);
    box-shadow:  0px 0px 30px 60px rgba(0, 0, 0, 0.7);
}
#commands .inner {
	padding: 20px;
	height: 300px;
	overflow: auto;
}
#commands a {
	color: #fff;
	text-decoration: none;
	position: absolute;
	top: 6px;
	right: 6px;
	background: #CC0000;
	padding: 0px 4px 0px 4px;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
}
#commands table {
    padding: 0;
    width: 99%;
    border-collapse: collapse;
}
#commands td {
	font-size: 8px;
	line-height: 15px;
	padding: 3px 0 3px 0;
	color: #bbb;
}
#commands td:first-child {
	padding-right: 10px;
	font-size: 9px;
	color: #fff;
}
#commands td.head {
	font-size: 11px;
	color: #fff;
	text-transform: uppercase;
}
.clearfix:after {
	content: ".";
	display: block;
	clear: both;
	visibility: hidden;
	line-height: 0;
	height: 0;
}
 
.clearfix {
	display: inline-block;
}
 
html[xmlns] .clearfix {
	display: block;
}
 
* html .clearfix {
	height: 1%;
}
</style>
</head>
<body>
<div id="commands">
<a href="#">&times;</a>
<div class="inner">
<table>
  <tr>
    <td class="head"><strong>Command</strong></td>
    <td class="head"><strong>Description</strong></td>
    <td class="head"><strong>Parameters</strong></td>
    <td class="head"><strong>Sample</strong></td>
  </tr>
  <tr>
    <td>last_changed</td>
    <td>shows the unique id of the players that spawned and/or last modified the selected structure</td>
    <td></td>
    <td>/last_changed</td>
  <tr>
    <td>teleport_to</td>
    <td>teleports the current controlled entity</td>
    <td>PlayerName(String), X(Float), Y(Float), Z(Float)</td>
    <td>/teleport_to schema 0.0 1.0 3.5</td>
  <tr>
    <td>kill_character</td>
    <td>kills the entity with that name</td>
    <td>PlayerName(String)</td>
    <td>/kill_character schema</td>
  <tr>
    <td>teleport_self_to</td>
    <td>teleports the current controlled entity</td>
    <td>X(Float), Y(Float), Z(Float)</td>
    <td>/teleport_self_to 0.0 1.0 3.5</td>
  <tr>
    <td>change_sector</td>
    <td>teleports the current player to another sector</td>
    <td>X(Integer), Y(Integer), Z(Integer)</td>
    <td>/change_sector 2 3 4</td>
  <tr>
    <td>export_sector</td>
    <td>exports the whole sector. be sure to use /force_save before</td>
    <td>X(Integer), Y(Integer), Z(Integer), name(String)</td>
    <td>/export_sector 2 3 4 mySavedSector</td>
  <tr>
    <td>import_sector</td>
    <td>make sure that the target sector is unloaded</td>
    <td>toX(Integer), toY(Integer), toZ(Integer), name(String)</td>
    <td>/import_sector 2 3 4 mySavedSector</td>
  <tr>
    <td>change_sector_for</td>
    <td>teleports any player to another sector</td>
    <td>player(String), X(Integer), Y(Integer), Z(Integer)</td>
    <td>/change_sector_for schema 2 3 4</td>
  <tr>
    <td>repair_sector</td>
    <td>attempts to correct the regitry of the sector</td>
    <td>X(Integer), Y(Integer), Z(Integer)</td>
    <td>/repair_sector 2 3 4</td>
  <tr>
    <td>teleport_self_home</td>
    <td>teleports the current controlled entity to the spawning point of the player controlling it</td>
    <td></td>
    <td>/teleport_self_home</td>
  <tr>
    <td>destroy_entity</td>
    <td>Destroys the selected Entity</td>
    <td></td>
    <td>/destroy_entity</td>
  <tr>
    <td>destroy_entity_dock</td>
    <td>Destroys the selected Entity and all docked ships</td>
    <td></td>
    <td>/destroy_entity_dock</td>
  <tr>
    <td>giveid</td>
    <td>Gives player elements by ID</td>
    <td>PlayerName(String), ElementID(Short), Count(Integer)</td>
    <td>/giveid schema 2 10</td>
  <tr>
    <td>give</td>
    <td>Gives player elements by NAME</td>
    <td>PlayerName(String), ElementName(String), Count(Integer)</td>
    <td>/give schema Power 10</td>
  <tr>
    <td>give_logbook</td>
    <td>Gives player logbook)</td>
    <td>PlayerName(String)</td>
    <td>/give_logbook schema</td>
  <tr>
    <td>give_laser_weapon</td>
    <td>Gives player logbook)</td>
    <td>PlayerName(String)</td>
    <td>/give_laser_weapon schema</td>
  <tr>
    <td>give_recipe</td>
    <td>Gives player recipe)</td>
    <td>PlayerName(String), TypeOutput(Integer)</td>
    <td>/give_recipe schema 1</td>
  <tr>
    <td>give_credits</td>
    <td>Gives player credits)</td>
    <td>PlayerName(String), Count(Integer)</td>
    <td>/give_credits schema 1000</td>
  <tr>
    <td>start_countdown</td>
    <td>Starts a countdown visible for everyone)</td>
    <td>Seconds(Integer), Message(String)</td>
    <td>/start_countdown 180 may contain spaces</td>
  <tr>
    <td>jump</td>
    <td>Jump to an object in line of sight if possible</td>
    <td></td>
    <td>/jump</td>
  <tr>
    <td>tp_to</td>
    <td>warp to player's position</td>
    <td>PlayerName(String)</td>
    <td>/tp_to schema</td>
  <tr>
    <td>tp</td>
    <td>warp a player to your position</td>
    <td>PlayerName(String)</td>
    <td>/tp schema</td>
  <tr>
    <td>ignore_docking_area</td>
    <td>enables/disables docking area validation (default off)</td>
    <td>enable(Boolean)</td>
    <td>/ignore_docking_area false</td>
  <tr>
    <td>shutdown</td>
    <td>shutsdown the server in specified seconds (neg values will stop any active countdown)</td>
    <td>TimeToShutdown(Integer)</td>
    <td>/shutdown 120</td>
  <tr>
    <td>force_save</td>
    <td>The server will save all data to disk</td>
    <td></td>
    <td>/force_save</td>
  <tr>
    <td>add_admin</td>
    <td>Gives admin rights to (param0(String)))</td>
    <td>PlayerName(String)</td>
    <td>/add_admin schema</td>
  <tr>
    <td>list_admins</td>
    <td>Lists all admins</td>
    <td></td>
    <td>/list_admins</td>
  <tr>
    <td>status</td>
    <td>Displays server status</td>
    <td></td>
    <td>/status</td>
  <tr>
    <td>list_banned_ip</td>
    <td>Lists all banned IPs</td>
    <td></td>
    <td>/list_banned_ip</td>
  <tr>
    <td>list_banned_name</td>
    <td>Lists all banned names</td>
    <td></td>
    <td>/list_banned_name</td>
  <tr>
    <td>list_whitelist_ip</td>
    <td>Lists all whitelisted IPs</td>
    <td></td>
    <td>/list_whitelist_ip</td>
  <tr>
    <td>list_whitelist_name</td>
    <td>Lists all whitelisted names</td>
    <td></td>
    <td>/list_whitelist_name</td>
  <tr>
    <td>ban_name</td>
    <td>bans a playername from this server</td>
    <td>PlayerName(String)</td>
    <td>/ban_name schema</td>
  <tr>
    <td>ban_ip</td>
    <td>bans a ip from this server</td>
    <td>PlayerIP(String)</td>
    <td>/ban_ip 192.0.0.1</td>
  <tr>
    <td>whitelist_name</td>
    <td>add a playername to the white list</td>
    <td>PlayerName(String)</td>
    <td>/whitelist_name schema</td>
  <tr>
    <td>whitelist_ip</td>
    <td>add an IP to the white list</td>
    <td>PlayerIP(String)</td>
    <td>/whitelist_ip 192.0.0.1</td>
  <tr>
    <td>whitelist_activate</td>
    <td>Turns white list on/off (will be saved in server.cfg)</td>
    <td>enable(Boolean)</td>
    <td>/whitelist_activate false</td>
  <tr>
    <td>unban_name</td>
    <td>unbans a playername from this server</td>
    <td>PlayerName(String)</td>
    <td>/unban_name schema</td>
  <tr>
    <td>unban_ip</td>
    <td>unbans a ip from this server</td>
    <td>PlayerIP(String)</td>
    <td>/unban_ip 192.0.0.1</td>
  <tr>
    <td>kick</td>
    <td>kicks a player from the server</td>
    <td>PlayerName(String)</td>
    <td>/kick schema</td>
  <tr>
    <td>update_shop_prices</td>
    <td>Updates the prices of all shops instantly</td>
    <td></td>
    <td>/update_shop_prices</td>
  <tr>
    <td>remove_admin</td>
    <td>Removes admin rights of player</td>
    <td>PlayerName(String)</td>
    <td>/remove_admin schema</td>
  <tr>
    <td>search</td>
    <td>Returns the sector of a ship of station with that uid </td>
    <td>ShipOrStationName(String)</td>
    <td>/search myLostShip</td>
  <tr>
    <td>sector_chmod</td>
    <td>Changes the sector mode: example '/sector_chmod 8 8 8 + peace', available modes are 'peace'(no enemy spawn), 'protect'(no attacking possible)</td>
    <td>SectorX(Integer), SectorY(Integer), SectorZ(Integer), +/-(String), peace/protect(String)</td>
    <td>/sector_chmod 10 12 15 10 10</td>
  <tr>
    <td>shop_restock</td>
    <td>Restocks the selected shop with items</td>
    <td></td>
    <td>/shop_restock</td>
  <tr>
    <td>god_mode</td>
    <td>enables god mode for a player</td>
    <td>PlayerName(String), active(Boolean)</td>
    <td>/god_mode schema true/false</td>
  <tr>
    <td>invisibility_mode</td>
    <td>enables invisibility mode for a player</td>
    <td>PlayerName(String), active(Boolean)</td>
    <td>/invisibility_mode schema true/false</td>
</table>
</div>
</div>
<div id="wrapper">
	<div style="width: 490px; float: left;">
	<h1>FeedUpYourBeast - LiveChat</h1>
	<div id="chat">
		<div id="inner">
			<?= $output ?>
		</div>
	</div>
	<div id="form">
		<form method="post" action="" id="chatform" name="chatform">
			<input type="text" id="text" name="text" placeholder="Text-Chat ..." autocomplete="off"><img src="terminal.png" data-active="chat" height="16" style="margin: 0 0 -4px -23px; cursor: pointer;">
		</form>
	</div>
	<div id="lastcmds">
		<img src="help.png" data-active="chat" height="16">
		<div class="inner">
		</div>
	</div>
	</div>
	<div style="width: 160px; float: right;">
		<h2>Who is online? <span>< 5 min.</span></h2><div id="count">0 online</div>
		<div id="players">
			<div class="inner">
			</div>
		</div>
		<h2>Add Admin</h2>
		<div id="addadmin">
			<?php include('admins.php'); ?>
		</div>
	</div>
</div>

<script type="text/javascript" src="jquery-latest.min.js"></script>
<script type="text/javascript">
$( document ).ready(function() {
	var chatbuffer = 0;
	var input_mode = "chat";
	var help_active = false;
	
	$("#lastcmds img").click(function() {
		if (help_active != true) {
			$('#commands').fadeIn(200);
			help_active = true;
		} else if (help_active != false) {
			$('#commands').fadeOut(200);
			help_active = false;
		}
	});
	$("#commands a").click(function() {
		if (help_active != false) {
			$('#commands').fadeOut(200);
			help_active = false;
		}
		return false;
	});
	$("#chatform img").click(function() {
		var ifactive = $(this).attr("data-active");
		$('#text').val('');
		if (ifactive == "chat") {
			$(this).fadeOut(50, function() {
				$(this).attr("src","chat.png").fadeIn(100);
			});
			input_mode = "terminal";
			$(this).attr("data-active", input_mode);
			
			$("#chatform #text").attr("placeholder","Command ...");
		} else if (ifactive == "terminal") {
			$(this).fadeOut(50, function() {
				$(this).attr("src","terminal.png").fadeIn(100);
			});
			input_mode = "chat";
			$(this).attr("data-active", input_mode);
			
			$("#chatform #text").attr("placeholder","Text-Chat ...");
		}
		$('#text').focus();
	});
	
	function reloadAdmins() {
		$.get( "admins.php", function( data ) {
			$("#addadmin").html(data);
			$("#addadmin a").click(function() {
				var getadmin = $(this).attr("class");
				var add_admin = "/add_admin " + getadmin;
				
				var getactive = $(this).parent().find('span').text();
				
				if (getactive == "active") {
					add_admin = "/remove_admin " + getadmin;
				}
				
				$.post( "sendcmd.php", { text: add_admin }, function (data) {
		    		$('#lastcmds .inner').prepend('<div>' + data + '</div>');
		    	});
		    	reloadAdmins();
				return false;
			});
		});
	}
	
	function refreshChat() {
		$.get( "getchat.php", function( data ) {
			$("#inner").html(data);
			
			$("#chat table tr").hover(
    			function() {
					$(this).css("background","rgba(255,255,255,.15)");
				},
    			function() {
					$(this).css("background","");
				}
			);
			
			if (chatbuffer.length != data.length) {
				chatbuffer = data;
    			$('#chat').scrollTop( $('#chat #inner').height());
    		}
		});
		getPlayers();
    	reloadAdmins();
	}
	function getPlayers() {
		$.get( "players.php", function( data ) {
			$("#players .inner").html(data);
			
			$("#players .inner li").hover(
    			function() {
					$(this).css("background","rgba(255,255,255,.15)");
				},
    			function() {
					$(this).css("background","");
				}
			);
			var count = $("#players .inner li").length;
			
			$("#count").html(count + ' online');
			
			$("#players .inner li img").click(function() {
				getnick = $(this).attr("data-nickname");
				if(confirm('Spieler ' + getnick + ' wirklich kicken?')) {
					$.post( "kickplayer.php", { name: getnick }, function (data) {
					});
				}
			});
		});
	}
	initChat = setInterval(function() {
      refreshChat();
	}, 3000);
	
	/*initAdmins = setInterval(function() {
      reloadAdmins();
	}, 60000);*/
	
	/*initPlayers = setInterval(function() {
      getPlayers();
	}, 10000);*/
	
	refreshChat();
	//reloadAdmins();
	//getPlayers();
	
	$('#chat').scrollTop( $('#chat #inner').height());
	$('#text').focus();
	
	var myformselector = "#chatform";
	$(myformselector).submit(function(e) {
	    e.preventDefault();
	    var actionurl = e.currentTarget.action;
	    if ($('#text').val().length >1) {
	    	getmode = $("#chatform img").attr("data-active");
	    	if (getmode == "chat") {
	    		$.post( "sendchat.php", $(myformselector).serialize(), function (data) {
	    			refreshChat();
	    			$('#text').val('');
	    		});
	    	}
	    	if (getmode == "terminal") {
	    		$.post( "sendcmd.php", $(myformselector).serialize(), function (data) {
	    			$('#text').val('').attr("placeholder", "Command executed (no text return)");
	    			$('#lastcmds .inner').prepend('<div>' + data + '</div>');
	    		});
	    	}
	    }
	});
});
</script>
</body>
</html>