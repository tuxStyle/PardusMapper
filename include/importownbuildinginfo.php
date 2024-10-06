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

// Building Main Page Variables
$loc = Request::pint(key: 'loc');
http_response(is_null($loc), ApiResponse::BADREQUEST, sprintf('loc query parameter is required or invalid: %s', $loc ?? 'null'));


$image = Request::pstring(key: 'img');
$name = Request::pstring(key: 'name');

// Building Trade Variables
$fs = Request::pstring(key: 'fs');
if (!is_null($fs)) { $fs = (int)str_replace(',', '', $fs); }

$bts = Request::pstring(key: 'bts');
$level = Request::pstring(key: 'level');
$bm = Request::pstring(key: 'bm');

// Get Map information
$m = DB::map(id: $loc, universe: $uni);
http_response(is_null($m), ApiResponse::BADREQUEST, sprintf('could not load map for location: %s', $loc ?? 'null'));

// Verify Building is already in DB Tables Add if Not
$b = DB::building(id: $loc, universe: $uni);
if ($b) {
	// Building in DB Verify Stock is in DB
	debug('Got Building Infomation checking Stock');
	$stocks = DB::stocks(id: $loc, universe: $uni);
	if (0 === count($stocks)) {
		DB::building_stock_add(universe: $uni, image: $m->fg, id: $loc);
	}
} else {
	// Building not in DB
	DB::building_add(universe: $uni, image: $m->fg, id: $loc, sb: 0);
	$b = DB::building(id: $loc, universe: $uni);
}

// Get Sector and Cluster Information from Location
$s = DB::sector(id:$loc);
$c = DB::cluster(id: $s->c_id);

$updateBuilding = [];

// Double Check that Cluster and Sector have been Set for the Building
if (is_null($b->cluster)) {
    $updateBuilding['cluster'] = $c->name;
}
if (is_null($b->sector)) {
    $updateBuilding['sector'] = $s->name;
}

// Collect Info

// REVIEW
// the tampermonkey script is sending the wrong name
// we receive the time left till the next production tick in the name field
// attempt to get the building name by building image

$p = DB::building_static(image: $image);
$name = !is_null($p) ? $p->name: $name;
$updateBuilding['name'] = $name;
$updateBuilding['image'] = $image;


if (isset($bts)) {
	// Visited Building Trade Settings Page
	debug('Visited Building Trade Settings Page', $name);
	$bts = explode('~', $bts);

	// Loop Through All Data
	$building_stock_level = 0;
	$building_stock_max = 0;

	for ($i = 1; $i < sizeof($bts); $i++) {
		$temp = explode(',', $bts[$i]);
        debug('Looking up infor for ' . $temp[0], $temp);

		// Calculate Stocking Level for this Resource
		$res = DB::upkeep_static(name: $name, res: $temp[0]);
		$stock_level = 0;
		if ($res->upkeep) {
			if ($temp[3]) {
				$stock_level = round(($temp[1] / $temp[3]) * 100, 0);
				if ($stock_level > 100) {
					$stock_level = 100;
				}
			}
			$building_stock_level += $temp[1];
			$building_stock_max += $temp[3];
		}

        $s = DB::building_stock(id: $loc, name: $temp[0], universe: $uni);
		if (is_null($s)) {
            DB::stock_create(id: $loc, name: $temp[0], universe: $uni);
		}

		debug('Stocking Level is ' . $stock_level);

        $updateStock = [];
        $updateStock['amount'] = (int)$temp[1];
        $updateStock['min'] = (int)$temp[2];
        $updateStock['max'] = (int)$temp[3];
        $updateStock['buy'] = (int)$temp[5];
        $updateStock['sell'] = (int)$temp[4];
        $updateStock['stock'] = (int)$stock_level;
	}

    DB::stock_update(id: $loc, name: $temp[0], params: $updateStock, universe: $uni);

	if ($building_stock_max) {
		$building_stock_level = round(($building_stock_level / $building_stock_max) * 100, 0);
		if ($building_stock_level > 100) {
			$building_stock_level = 100;
		}
	}

    debug('Stocking Stock Level is ' . $building_stock_level);
    $updateBuildingStock['stock'] = $building_stock_level;
    DB::building_stock_update(id: $loc, params: $updateBuildingStock, universe: $uni);
}

if (isset($level)) {
    debug('Getting Building Level For: ' . $name);
	$temp = explode(',', $level);
    debug($temp);

    $u = $res = DB::upkeep_static(name: $name, res: $temp[0], upkeep: 0);
	if ($u) {
        debug('Found ' . $u->res);

		$i = 1;
		while (($temp[1] != production($u->amount, $i)) && ($i <= 20)) {
			$i++;
		}

        debug('Guessing Level ' . $i);

		if ($i <= 20) {
            DB::building_stock_update(id: $loc, params: ['level' => $i], universe: $uni);
		}
	}
}

if (isset($bm)) {
	// Visited Building Management Page
	debug('Visited Building Management Page');

	//if (!$b->x && !$b->y) {
	$x = Coordinates::getX($loc, $s->s_id, $s->rows);
	$y = Coordinates::getY($loc, $s->s_id, $s->rows, $x);
    $updateBuilding['x'] = $x;
    $updateBuilding['y'] = $y;
	//}

	$cap = $fs;
	$building_stock_level = 0;
	$building_stock_max = $fs;


	if ($name == "Trading Outpost") {
		$bm = explode('~', $bm);
		debug('Trading Outpost', $bm);

		for ($i = 0; $i < sizeof($bm); $i++) {
			$temp = explode(',', $bm[$i]);
			$cap += $temp[1];

            $s = DB::building_stock(id: $loc, name: $temp[0], universe: $uni);
            if (is_null($s)) {
                DB::stock_create(id: $loc, name: $temp[0], universe: $uni);
            }

            DB::stock_update(id: $loc, name: $temp[0], params: ['amount' => (int)$temp[1]], universe: $uni);
		}
	} else {

		debug('Not a Trading Outpost it is a ' . $name);
		// Get list of Resources for Building
        $resources = [];
        $res = DB::upkeep_static(name: $name);
		foreach($res as $u) {$resources[] = $u;}

		// Loop through List
		foreach ($resources as $u) {
			debug('Checking ' . str_replace(" ", "_", $u->res));

            $q = DB::building_stock(id: $loc, name: $u->res, universe: $uni);
            if (is_null($q)) {
                DB::stock_create(id: $loc, name: $u->res, universe: $uni);
                $q = DB::building_stock(id: $loc, name: $u->res, universe: $uni);
            }
            debug('Stock', $q);

            $updateStock = [];
            $res = Request::pint(key: str_replace(" ", "_", $u->res));
			if (isset($res)) {
				debug('We have info for ' . $u->res);

				// We have information for this Resource Update DB
				$cap += $res;
				$stock = 0;

				if ($q->max > 0) {
					if ($u->upkeep) {
						$building_stock_level += $res;
						$building_stock_max += $q->max;
					}
					$stock = round(($res / $q->max) * 100, 0);
					if ($stock > 100) {
						$stock = 100;
					}
				}

				// Update `' . $uni . '_Stock with Resource Information
                $updateStock['amount'] = $res;
                $updateStock['stock'] = $stock;
			} else {
				debug('No info for ' . $u->res);

				// No info for this resource so set values to 0
                $updateStock['amount'] = 0;
                $updateStock['stock'] = 0;
			}

            DB::stock_update(id: $loc, name: $u->res, params: $updateStock, universe: $uni);
		}
	}

    debug('Stocking Stock Level is ' . $building_stock_level);
    $updateBuildingStock = [];
    $updateBuildingStock['stock'] = (int)$building_stock_level;
    $updateBuildingStock['capacity'] = (int)$cap;
    $updateBuildingStock['freespace'] = (int)$fs;
    DB::building_stock_update(id: $loc, params: $updateBuildingStock, universe: $uni);


    DB::building_update(id: $loc, params: $updateBuilding, universe: $uni);
}
