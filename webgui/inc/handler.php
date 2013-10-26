<?php
switch ($_GET['a']) {
    case 'cmd':
        $sendcmd = shell_exec("sudo -u starmade /home/starmade/StarMadeServer/sendcmd.sh '".$_POST['text']."'");
		echo $_POST['text'];
        break;
    case 'chat':
        $sendchat = shell_exec("sudo -u starmade /home/starmade/StarMadeServer/chat.sh '".$_POST['text']."'");
        break;
    case 'kick':
		$kickplayer = shell_exec("sudo -u starmade /home/starmade/StarMadeServer/kickplayer.sh '".$_POST['name']."'");
        break;
}
?>