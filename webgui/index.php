<?php
header("Content-Type: text/html; charset=utf-8");
?>
<!--
 ________  ________ ______                _                 _ 
/  ___|  \/  /  __ \| ___ \              | |               (_)
\ `--.| .  . | /  \/| |_/ / __      _____| |__   __ _ _   _ _ 
 `--. \ |\/| | |    |  __/  \ \ /\ / / _ \ '_ \ / _` | | | | |
/\__/ / |  | | \__/\| |      \ V  V /  __/ |_) | (_| | |_| | |
\____/\_|  |_/\____/\_|       \_/\_/ \___|_.__/ \__, |\__,_|_|
                                                 __/ |        
                                                |___/         
-->
<!doctype html>
<html>
<head>
<title>SMCP webgui - FeedUpYourBeast</title>
<link href="css/main.css" rel="stylesheet">
</head>
<body>
<div id="wrapper">
	<div class="left">
	<h1>SMCP webgui</h1>
	<h3>FeedUpYourBeast - LiveChat</h3>
	<div id="chat">
		<div id="inner">
		</div>
	</div>
	<div id="form">
		<form method="post" action="" id="chatform" name="chatform">
			<input type="text" id="text" name="text" placeholder="Text-Chat ..." autocomplete="off"><img src="img/terminal.png" data-active="chat" height="16">
		</form>
	</div>
	<div id="lastcmds">
		<img src="img/help.png" data-active="chat" height="16">
		<div class="inner">
		</div>
	</div>
	</div>
	<div class="right">
		<h2>Who is online? <span>< 5 min.</span></h2><div id="count">0 online</div>
		<div id="players">
			<div class="inner">
			</div>
		</div>
		<h2>Add Admin</h2>
		<div id="addadmin">
			<img src="img/info.png" height="16">
			<div class="inner">
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="js/jquery-latest.min.js"></script>
<script type="text/javascript" src="js/main.js"></script>
</body>
</html>