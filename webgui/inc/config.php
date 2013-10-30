<?php
// Memory Limit 256 MegaByte because of Logs
@ini_set("memory_limit",'256M');

// Switch System on or off
$status = "on";

// Directory buffer logs
$chat_log   = 'chat.log';
$player_log = 'players.log';
$log_dir    = '/var/www/feedupyourbeast.de/chat/logs/';

// Home StarMade Server Directory
$starmadedir = '/home/starmade/StarMadeServer';

// Admin log
$admin_file = $starmadedir.'/StarMade/admins.txt';

// 5.5 minutes check player online status
$minoffset = 330;

// All Admins
$admins = array(
'Ret0rus',
'Tuvian',
'ClipKlap',
'fredforlaut',
'Zodiak',
'foxteladi',
'IshtarStar'
);

// How many logs to parse
$filedepth = 10;

// Smilie string replace
$strsmilie = array(
':D', //  1
';)', //  2
':)', //  3
':P', //  4
':(', //  5
'xD', //  6
'XD', //  7
'xd', //  8
'X)', //  9
'XDD', // 10
':o', //  11
':O', //  12
':0', //  13
'(:', //  14
'(;', //  15
';D', //  16
':/', //  17
'/:', //  18
);
$imgsmilie = array(
'<img src="img/smilie/s1.png" height="15">', // 1
'<img src="img/smilie/s2.png" height="15">', // 2
'<img src="img/smilie/s3.png" height="15">', // 3
'<img src="img/smilie/s4.png" height="15">', // 4
'<img src="img/smilie/s5.png" height="15">', // 5
'<img src="img/smilie/s6.png" height="15">', // 6
'<img src="img/smilie/s6.png" height="15">', // 7
'<img src="img/smilie/s6.png" height="15">', // 8
'<img src="img/smilie/s6.png" height="15">', // 9
'<img src="img/smilie/s6.png" height="15">', // 10
'<img src="img/smilie/s7.png" height="15">', // 11
'<img src="img/smilie/s7.png" height="15">', // 12
'<img src="img/smilie/s7.png" height="15">', // 13
'<img src="img/smilie/s1.png" height="15">', // 14
'<img src="img/smilie/s2.png" height="15">', // 15
'<img src="img/smilie/s2.png" height="15">', // 16
'<img src="img/smilie/s8.png" height="15">', // 17
'<img src="img/smilie/s8.png" height="15">', // 18
);

?>