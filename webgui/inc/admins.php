<?php
include_once('config.php');

// Parse admins.txt StarMade File
$admin_file = $starmadedir.'/admins.txt';

$admins_active = array();

if (file_exists($admin_file)) {
	$handle = @fopen($admin_file, "r");
	if ($handle) {
    	while (($buffer = fgets($handle, 4096)) !== false) {
    		if (strlen($buffer) > 3) {
    			$admins_active[] = str_replace(array('\r'),array(''),trim($buffer));
    		}
    	}
    }
}

?>
<table>
<?php
// Output Admins from config.php -> $admins
foreach($admins as $a) {

	$style = '';
	$active = '';
	if (in_array($a,$admins_active,true)) {
		$style = ' style="color: #cc0000 !important;"';
		$active = '<span>active</span>';
	}
?>
	<tr>
		<td><a href="#" class="<?= $a ?>"<?= $style ?>><?= $a ?></a><?= $active ?></td>
	</tr>
<?php
}
?>
</table>