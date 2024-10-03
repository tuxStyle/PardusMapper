<?php
declare(strict_types=1);
require_once('../app/settings.php');

use Pardusmapper\Core\MySqlDB;
use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Post;
use Pardusmapper\CORS;
use Pardusmapper\DB;

CORS::mapper();

if ($debug) xd($_POST);

$db = new MySqlDB();  // Create an instance of the Database class

$uni = Post::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

$sector = Post::sector();
$cluster = Post::cluster();

$npc_list = [];
$return = '';

// Initialize an array to hold the results
$static = DB::static_locations();

if (isset($sector)) {
	$db->execute(sprintf('SELECT DISTINCT name, id FROM %s_Test_Npcs WHERE sector = ? and (deleted is null or deleted = 0) GROUP BY name', $uni), [
        's', $sector
    ]);
} elseif (isset($cluster)) {
	if ($cluster != 'CORE') {
        $c = DB::cluster(code: $cluster);
		$db->execute(sprintf('SELECT DISTINCT name, id FROM %s_Test_Npcs WHERE cluster = ? and (deleted is null or deleted = 0) GROUP BY name', $uni), [
            's', $c->name
        ]);
	} else {
		$db->execute(sprintf('SELECT DISTINCT name, id FROM %s_Test_Npcs WHERE cluster LIKE \'Pardus%Contingent\' and (deleted is null or deleted = 0) GROUP BY name', $uni));
	}
} else {
	$db->execute(sprintf('SELECT DISTINCT name, id FROM %s_Test_Npcs Where (deleted is null or deleted = 0) GROUP BY name', $uni));
}
while ($n = $db->nextObject()) { if (!(in_array($n->id,$static))) { $npc_list[] = $n->name; } }
xp($n);
if ($npc_list) {array_unshift($npc_list,'All');}

$return .= '<table><tr><th>NPCs</th></tr>';
if ($npc_list) {foreach ($npc_list as $n) {
	$return .= '<tr><td><a href=# onclick="loadNPC(\'' . $n . '\',1);">' . $n . '</a></td></tr>';
}}
$return .= '</table>';

echo $return;

$db->close();
$db = null;
