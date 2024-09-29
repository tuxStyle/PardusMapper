<?php
declare(strict_types=1);
require_once('../app/settings.php');

use Pardusmapper\Core\Settings;
use Pardusmapper\Core\MySqlDB;

header('Access-Control-Allow-Origin: ' . Settings::$BASE_URL);

$db = new MySqlDB();  // Create an instance of the Database class

$uni = $db->protect($_POST['uni']);

$npc_list = [];
$return = '';

$db->query("SELECT * FROM Pardus_Static_Locations ");
while ($c = $db->nextObject()) { $static[] = $c->id; }

if (isset($_POST['sector'])) {
	$db->query('SELECT DISTINCT name, id FROM ' . $uni . '_Test_Npcs WHERE sector = \'' . $db->protect($_POST['sector']) . '\' and (deleted is null or deleted = 0) GROUP BY name');
} elseif (isset($_POST['cluster'])) {
	if ($_POST['cluster'] != 'CORE') {
		$db->query('SELECT * FROM Pardus_Clusters WHERE code = \'' . $db->protect($_POST['cluster']) . '\'');
		$c = $db->nextObject();
		$db->query('SELECT DISTINCT name, id FROM ' . $uni . '_Test_Npcs WHERE cluster = \'' . $c->name . '\' and (deleted is null or deleted = 0) GROUP BY name');
	} else {
		$db->query('SELECT DISTINCT name, id FROM ' . $uni . '_Test_Npcs WHERE cluster LIKE \'Pardus%Contingent\' and (deleted is null or deleted = 0) GROUP BY name');
	}
} else {
	$db->query('SELECT DISTINCT name, id FROM ' . $uni . '_Test_Npcs Where (deleted is null or deleted = 0) GROUP BY name');
}
while ($n = $db->nextObject()) { if (!(in_array($n->id,$static))) { $npc_list[] = $n->name; } }

if ($npc_list) {array_unshift($npc_list,'All');}

$return .= '<table><tr><th>NPCs</th></tr>';
if ($npc_list) {foreach ($npc_list as $n) {
	$return .= '<tr><td><a href=# onclick="loadNPC(\'' . $n . '\',1);">' . $n . '</a></td></tr>';
}}
$return .= '</table>';

echo $return;

$db->close();
$db = null;
