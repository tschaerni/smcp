<?php
$sendcmd = shell_exec("sudo -u starmade /home/starmade/StarMadeServer/sendcmd.sh '".$_POST['text']."'");
echo $_POST['text'];
?>