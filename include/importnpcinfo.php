<?php
declare(strict_types=1);

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;
use Pardusmapper\CORS;
use Pardusmapper\Request;

require_once('../app/settings.php');

CORS::pardus();

debug($_REQUEST);

// Set Univers Variable
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

// Get Version
$minVersion = 5.8;
$version = Request::pint(key: 'version', default: 0);
http_response($version < $minVersion, ApiResponse::BADREQUEST, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));

// Get Location
// Building Main Page Variables
$loc = Request::pint(key: 'loc');
http_response(is_null($loc), ApiResponse::BADREQUEST, sprintf('loc query parameter is required or invalid: %s', $loc ?? 'null'));

$image = Request::pstring(key: 'img');
$nid = Request::pint(key: 'nid');
$dead = Request::pbool('dead');

// Set Hull, Armor, Shield Levels
$hull = Request::pint(key: 'hull', default: 0);
$armor = Request::pint(key: 'armor', default: 0);
$shield = Request::pint(key: 'shield', default: 0);

$db = MySqlDB::instance();


$db->execute(sprintf('SELECT * FROM %s_Maps M inner join %s_Test_Npcs TN on M.id = TN.id and TN.deleted is null where M.id = ?', $uni, $uni), [
    'i', $loc
]);
$m = $db->nextObject();

if (is_null($m->npc)) {
    debug('Inserting New Info into DB');
	$db->addNPC($uni, $image, $loc, "", 0, 0, $nid);
} elseif ($dead) {
	debug('You killed it, good job...removing NPC');
	$db->removeNPC($uni, $loc);
} elseif ($m->npc == $image) {
    debug('Updating Hull, Armor, and Shield');
	$db->updateNPCHealth($uni, $loc, $hull, $armor, $shield, $nid);
} else {
    debug($m->npc . 'Removing Old NPC adding New<br>');
	$db->removeNPC($uni, $loc);
	$db->addNPC($uni, $image, $loc, "", 0, 0, $nid);
}

$db->close();
$db = null;
