<?php

// Memory Limit 265 MegaByte because of Logs
@ini_set("memory_limit",'256M');

// Home StarMade Server Directory
$starmadedir = '/home/starmade/StarMadeServer/StarMade';

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
);

?>