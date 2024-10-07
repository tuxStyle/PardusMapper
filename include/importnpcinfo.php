<?php
declare(strict_types=1);

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;
use Pardusmapper\CORS;
use Pardusmapper\DB;
use Pardusmapper\Request;

require_once('../app/settings.php');

CORS::pardus();

$db = MySqlDB::instance(); // Create an instance of the Database class

debug($_REQUEST);

// Set Univers Variable
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

// Get Version
$minVersion = 5.8;
$version = Request::pfloat(key: 'version', default: 0);
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

$db->execute(sprintf('SELECT * FROM %s_Maps M inner join %s_Test_Npcs TN on M.id = TN.id and TN.deleted is null where M.id = ?', $uni, $uni), [
    'i', $loc
]);
$m = $db->nextObject();

if (is_null($m->npc)) {
    debug('Inserting New Info into DB');
    DB::npc_add(universe: $uni, image: $image, id: $loc, sector: null, x: 0, y: 0, nid: $nid);
} elseif ($dead) {
    debug('You killed it, good job...removing NPC');
    DB::npc_remove(universe: $uni, id: $loc);
} elseif ($m->npc == $image) {
    debug('Updating Hull, Armor, and Shield');
    DB::npc_update_health(universe: $uni, id: $loc, hull: $hull, armor: $armor, shield: $shield, nid: $nid);
} else {
    debug($m->npc . 'Removing Old NPC adding New<br>');
    DB::npc_remove(universe: $uni, id: $loc);
    DB::npc_add(universe: $uni, image: $image, id: $loc, sector: null, x: 0, y: 0, nid: $nid);
}
