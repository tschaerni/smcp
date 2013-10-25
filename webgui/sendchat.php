<?php
$sendchat = shell_exec("sudo -u starmade /home/starmade/StarMadeServer/chat.sh '".$_POST['text']."'");
?>