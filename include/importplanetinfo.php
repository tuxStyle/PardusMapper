<?php
declare(strict_types=1);

use Pardusmapper\Coordinates;
use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;
use Pardusmapper\CORS;
use Pardusmapper\DB;
use Pardusmapper\Request;

require_once('../app/settings.php');

CORS::pardus();

debug($_REQUEST);

// Set Univers Variable
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

// Get Version
$minVersion = 5.8;
$version = Request::pfloat(key: 'version', default: 0);
http_response($version < $minVersion, ApiResponse::BADREQUEST, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));

$loc = Request::pint(key: 'loc');
http_response(is_null($loc), ApiResponse::BADREQUEST, sprintf('location(loc) query parameter is required or invalid: %s', $loc ?? 'null'));

$faction = Request::pstring(key: 'faction');
if (!is_null($faction)) { $faction = str_replace('64x64', '16x16', $faction); } 	// Resize Faction Image

$planet = Request::pbool(key: 'planet');
$image = Request::pstring(key: 'img');
$name = Request::pstring(key: 'name');
$pop = Request::pint(key: 'pop');
$crime = Request::pstring(key: 'crime');
$faction = Request::pstring(key: 'faction');
$credit = Request::pint(key: 'credit');
$pt = Request::pstring(key: 'pt');

// Get Sector And Cluster Info
$s = DB::sector(id:$loc);
$c = DB::cluster(id: $s->c_id);

$updateBuilding = [];
$updateBuildingStock = [];
$updateStock = [];

// Get Map information
// Get Map information
$m = DB::map(id: $loc, universe: $uni);
// Stop of map not found
http_response(is_null($m), ApiResponse::BADREQUEST, sprintf('map not found for location: %s', $loc));

$b = DB::building(id: $loc, universe: $uni);
if ($b) {
    // Building in DB, Verify Stock is in DB
    $stocks = DB::stocks(id: $loc, universe: $uni);
    if (count($stocks) < 1) {
        DB::building_stock_add(universe: $uni, image: $m->fg, id: $loc);
    }
} else {
    // Building not in DB
    DB::building_add(universe: $uni, image: $m->fg, id: $loc, sb: 0);
    $b = DB::building(id: $loc, universe: $uni);
}

// Double Check that Cluster and Sector have been Set for the Building
if (is_null($b->cluster)) {
    $updateBuilding['cluster'] = $c->name;
}
if (is_null($b->sector)) {
    $updateBuilding['sector'] = $s->name;
}

if ($planet) {
	// Visited Planet
    debug('Visited Planet');

	// Collect Info

	if (!$b->x && !$b->y) {
		$x = Coordinates::getX($loc, $s->s_id, $s->rows);
		$y = Coordinates::getY($loc, $s->s_id, $s->rows, $x);
        $updateBuilding['x'] = $x;
        $updateBuilding['y'] = $y;
	}

    $updateBuilding['name'] = $name;
    $updateBuilding['image'] = $image;
    $updateBuilding['population'] = $pop;
    $updateBuilding['crime'] = $crime;
    $updateBuilding['faction'] = $faction;

	if (isset($faction)) {
		debug('Updating Faction');
	} else {
        debug('Nulling Faction');
	}
}

if (isset($pt)) {
	// Visited Planet Trade
    debug('Visited Planet Trade', $pt);

	// Find out what type of planet
    $p = DB::building_static(image: $m->fg);
    debug($loc . ' Planet Type = ' . $p->name);

	// Collect Info
	$cap = 0;
	$building_stock_level = 0;
	$building_stock_max = 0;

	$pt = explode('~', $pt);
    debug($pt);

	// Loop through all pt data
	for ($i = 1; $i < sizeof($pt); $i++) {
		$temp = explode(',', $pt[$i]);
        debug($temp);

		$cap += $temp[1];
        $u = DB::upkeep_static(name: $p->name, res: $temp[0]);
		if ($u && $u->upkeep) {
			$building_stock_level += $temp[1];
			$building_stock_max += $temp[3];
		}
		$stock = 0;
		if ($temp[3]) {
			$stock = round(($temp[1] / $temp[3]) * 100, 0);
			if ($stock > 100) {
				$stock = 100;
			}
		}

		debug('Stocking for ' . $temp[0] . ' = ' . $stock);

        $stocks = DB::stocks(id: $loc, name: $temp[0], universe: $uni);
		if (0 === count($stocks)) {
            DB::stock_create(id: $loc, name: $temp[0], universe: $uni);
		}

        $updateStock['amount'] = (int)$temp[1];
        $updateStock['bal'] = (int)$temp[2];
        $updateStock['min'] = 0;
        $updateStock['max'] = (int)$temp[3];
        $updateStock['buy'] = (int)$temp[4];
        $updateStock['sell'] = (int)$temp[5];
        $updateStock['stock'] = (int)$stock;

        DB::stock_update(id: $loc, name: $temp[0], params: $updateStock, universe: $uni);
	}

	// Set Building Stock level
	if ($building_stock_max) {
		$building_stock_level = round(($building_stock_level / $building_stock_max) * 100, 0);
		if ($building_stock_level > 100) {
			$building_stock_level = 100;
		}
	}

	debug('Building Stock Level ' . $building_stock_level);
    $updateBuildingStock['capacity'] = (int)$cap;
    $updateBuildingStock['credit'] = (int)$credit;
    $updateBuildingStock['stock'] = (int)$building_stock_level;

    DB::building_stock_update(id: $loc, params: $updateBuildingStock, universe: $uni);
}

DB::building_update(id: $loc, params: $updateBuilding, universe: $uni);
