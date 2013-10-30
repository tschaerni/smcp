<?php
include('config.php');

switch ($_GET['a']) {
	case 'status':
		$output = shell_exec("sudo -u starmade ".$starmadedir."/functionlib.sh status");
		echo $output;
	break;
	case 'cmd':
        $sendcmd = shell_exec("sudo -u starmade ".$starmadedir."/functionlib.sh command '".$_POST['text']."'");
		echo $_POST['text'];
    break;
    case 'chat':
        $sendmsg = shell_exec("sudo -u starmade ".$starmadedir."/functionlib.sh msg '".$_POST['text']."'");
    break;
    case 'kick':
        $sendcmd = shell_exec("sudo -u starmade ".$starmadedir."/functionlib.sh command '/kick ".$_POST['name']."'");
    break;
    case 'dbsize':
        $output = shell_exec("sudo -u starmade ".$starmadedir."/functionlib.sh dbsize");
        echo 'Database size = '.str_replace(array(' ','total','G'),array('','',' GigaByte'),$output);
    break;
    case 'status':
        $output = shell_exec("sudo -u starmade ".$starmadedir."/functionlib.sh status");
        echo $output;
    break;
    case 'emergency':
        $emergency = shell_exec("sudo -u starmade ".$starmadedir."/functionlib.sh emergency");
        echo 'Emergency shutdown initiated -> please check';
    break;
    case 'kill':
        $kill = shell_exec("sudo -u starmade ".$starmadedir."/functionlib.sh kill");
        echo 'StarMade Server get killed -> please check';
    break;
    case 'mobs':
        $output = shell_exec("sudo -u starmade ".$starmadedir."/functionlib.sh mobs");
        echo 'There are '.preg_replace("/[^0-9]/","",$output).' MOBs in database';
    break;
    case 'cleanmob':
        $cleanmob = shell_exec("sudo -u starmade ".$starmadedir."/functionlib.sh cleanmob");
        echo 'Take a while to despawn all MOBs -> lag spike';
    break;
}
?>