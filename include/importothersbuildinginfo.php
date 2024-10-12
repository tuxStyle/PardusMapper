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

$db = MySqlDB::instance(); // Create an instance of the Database class

debug($_REQUEST);

// Set Univers Variable
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::OK, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

// Get Version
$minVersion = '5.8';
$version = Request::pstring(key: 'version', default: '0.0');
http_response(version_compare($version, $minVersion, '<'), ApiResponse::OK, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));


// Building Main Page Variables
$loc = Request::pint(key: 'loc');
http_response(is_null($loc), ApiResponse::OK, sprintf('loc query parameter is required or invalid: %s', $loc ?? 'null'));

$faction = Request::pstring(key: 'faction');
$alliance = Request::pstring(key: 'alliance');
$image = Request::pstring(key: 'img');
$name = Request::pstring(key: 'name');
$condition = Request::pint(key: 'condition');
$owner = Request::pstring(key: 'owner');

// Building Trade Variables
$fs = Request::pstring(key: 'fs');
if (!is_null($fs)) { $fs = str_replace(',', '', $fs); }
$credit = Request::pint(key: 'credit');

// Extra
$bt = Request::pstring(key: 'bt');
if (!is_null($bt)) {$bt = explode('~', $bt); }

// Get Map information
$m = DB::map(id: $loc, universe: $uni);
http_response(is_null($m), ApiResponse::OK, sprintf('could not load map for location: %s', $loc ?? 'null'));

// Verify Building is already in DB Tables Add if Not
$b = DB::building(id: $loc, universe: $uni);
if ($b) {
    // Building in DB - Verify Stock is in DB
    if (!str_contains("sb_", $m->fg)) {  // Correct usage of strpos
        debug('Checking Stocking Info');

        $stocks = DB::stocks(id: $loc, universe: $uni);
        if (0 === count($stocks)) {
            DB::building_stock_add(universe: $uni, image: $m->fg, id: $loc);
        }
    }
} else {
    // Building not in DB
    DB::building_add(universe: $uni, image: $m->fg, id: $loc, sb: 0);
    $b = DB::building(id: $loc, universe: $uni);
}

debug($b);

if (str_contains("sb_", $m->fg)) {
    debug('We are Flying Close to a SB');

    $db->execute(sprintf('SELECT * FROM %s_Buildings where starbase < ? ORDER BY starbase DESC LIMIT 1', $uni), [
        'i', $loc
    ]);
    $q = $db->nextObject();
    $x = Coordinates::getX($loc, $q->starbase, 13);
    $y = Coordinates::getY($loc, $q->starbase, 13, $x);
    $s = DB::sector(id: $q->id);
} else {
    debug('Get location information for location: ' . $loc);

    // Get Sector and Cluster Information from Location
    $s = DB::sector(id: $loc);
    $x = Coordinates::getX($loc, $s->s_id, $s->rows);
    $y = Coordinates::getY($loc, $s->s_id, $s->rows, $x);
}
$c = DB::cluster(id: $s->c_id);

$updateBuilding = [];

// Double Check that Cluster and Sector have been Set for the Building
if (is_null($b->cluster)) {
    $updateBuilding['cluster'] = $c->name;
}
if (is_null($b->sector)) {
    $updateBuilding['sector'] = $s->name;
}
if (!$b->x && !$b->y) {
    $updateBuilding['x'] = $x;
    $updateBuilding['y'] = $y;
}

if (isset($_REQUEST['building'])) {
    // Visited a Building
    debug('Visited a Building');

    $p = DB::building_static(image: $image);

    // Collect Info

    // REVIEW
    // the tampermonkey script is sending the wrong name
    // we receive the owner name in both name and owner fields
    // attempt to get the building name by building image
    $name = !is_null($p) ? $p->name: $name;
    $updateBuilding['name'] = $name;
    $updateBuilding['image'] = $image;
    $updateBuilding['condition'] = (int)$condition;
    $updateBuilding['owner'] = $owner;
    $updateBuilding['alliance'] = $alliance;
    $updateBuilding['faction'] = $faction;

    if (isset($owner)) {
        debug('Updating owner');
    } else {
        debug('Nulling owner');
    }
    if (isset($alliance)) {
        debug('Updating alliance');
    } else {
        debug('Nulling alliance');
    }
    if (isset($faction)) {
        debug('Updating faction');
    } else {
        debug('Nulling faction');
    }

    // If we can see the Building then there are no NPCs at this location
    DB::npc_remove(universe: $uni, id: $loc, deleteMissions: true);
}
if (isset($bt)) {
    // Visited Building Trade
    debug('Visited Building Trade');

    // Collect Info
    $updateBuilding['image'] = $image;
    $updateBuilding['name'] = $name;

    //loc=327655&bt=~Food,48,0,66,9999,120~Energy,48,0,66,9999,50~Water,48,0,66,9999,100~Ore,108,0,132,9999,150~Metal,63,0,0,400,0&fs=55&credit=2826766
    $cap = $fs;

    //$date = getdate(strtotime($b->stock_updated));
    //$tick = mktime(1,25,0,$date['mon'],$date['mday'],$date['year']);
    $ts = strtotime($b->stock_updated);
    $date = new DateTime("@$ts");
    $date->setTime(1, 25, 0);
    $tick = $date->format('U');

    while ($tick < strtotime($b->stock_updated)) {
        $tick += (60 * 60 * 6);
    }
    $i = 0;
    while ($tick < strtotime($b->stock_updated)) {
        $tick += (60 * 60 * 6);
        $i++;
    }

    if ($i) {
        $i++;
    }

    $tick = $i;

    // Get Upkeep Info
    debug('Building is ' . $b->name);
    for ($x = 1; $x <= 20; $x++) {
        $level[$x] = 0;
    }
    //Loop through all bt data
    $building_stock_level = 0;
    $building_stock_max = 0;

    for ($i = 1; $i < sizeof($bt); $i++) {
        $temp = explode(',', $bt[$i]);
        debug($temp);
        debug('Capacity: ' . $cap);
        $temp[1] = str_replace(',', '', $temp[1]); // Remove commas from the second element
        $cap += $temp[1];

        // REVIEW:
        //
        // Original query was using the name of the building from database
        //		$db->query('SELECT * FROM Pardus_Upkeep_Data WHERE name = \'' . $b->name . '\' AND res = \'' . $temp[0] . '\'');
        //
        // The upkeep table uses building type Space Farm for example not the owner of the of the building
        // so, this query was not returning anything
        //
        // The building visited request sends name same as the owner name so, this is saved in database
        // and $b->name contais the owner name instead of building type
        // Array
        //     (
        //         [uni] => Pegasus
        //         [version] => 6.9
        //         [loc] => 148874
        //         [building] => 1
        //         [name] => Wight Dread Viserion
        //         [img] => foregrounds/space_farm.png
        //         [owner] => Wight Dread Viserion
        //         [faction] => factions/sign_uni_16x16.png
        //         [alliance] => The Ebidium Dagger
        //         [condition] => 100
        //     )
        //
        // The trade request sends name as Space Farm so, using name from here
        // Array
        //     (
        //         [uni] => Pegasus
        //         [version] => 6.9
        //         [loc] => 148874
        //         [name] => Space Farm
        //         [img] => foregrounds/space_farm.png
        //         [bt] => ~Food,231,0,0,150,0~Energy,150,105,210,200,50~Water,56,0,0,80,0~Animal embryos,10,133,266,200,30~Bio-waste,31,0,0,40,0
        //         [fs] => 218
        //         [credit] => 0
        //     )


        $u = DB::upkeep_static(fg: $image, res: $temp[0]);
        debug($u);
        $amount = $u->amount;
        $upkeep = $u->upkeep;

        $s = DB::building_stock(id: $loc, name: $temp[0], universe: $uni);
        if ($tick && $s) {
            debug('Using ' . $temp[0] . ' base amount ' . $amount);

            if ($upkeep) {
                $diff = $s['amount'] - $temp[1];
            } else {
                $diff = $temp[1] - $s['amount'];
            }
            debug('Difference is ' . $diff);
            for ($j = 1; $j <= 20; $j++) {
                if ($upkeep) {
                    debug('Trying Level ' . $j . ' value of ' . upkeep($amount, $j));
                    if ($diff == (upkeep($amount, $j) * $tick)) {
                        $level[$j]++;
                    }
                } else {
                    debug('Trying Level ' . $j . ' value of ' . production($amount, $j));
                    if ($diff == (production($amount, $j) * $tick)) {
                        $level[$j]++;
                    }
                }
            }
        }
        if ($upkeep) {
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

        $s = DB::building_stock(id: $loc, name: $temp[0], universe: $uni);
        if (is_null($s)) {
            DB::stock_create(id: $loc, name: $temp[0], universe: $uni);
        }

        $updateStock = [];
        $updateStock['amount'] = (int)$temp[1];
        $updateStock['bal'] = 0;
        $updateStock['min'] = (int)$temp[2];
        $updateStock['max'] = (int)$temp[3];
        $updateStock['buy'] = (int)$temp[4];
        $updateStock['sell'] = (int)$temp[5];
        $updateStock['stock'] = (int)$stock;
        DB::stock_update(id: $loc, name: $temp[0], params: $updateStock, universe: $uni);
    }

    $updateBuildingStock = [];
    $updateBuildingStock['capacity'] = $cap;
    $updateBuildingStock['freespace'] = $fs;
    $updateBuildingStock['credit'] = $credit;

    // Set Building Stock level
    if ($building_stock_max) {
        $building_stock_level = round(($building_stock_level / $building_stock_max) * 100, 0);
        if ($building_stock_level > 100) {
            $building_stock_level = 100;
        }
    }

    debug('Building Stock Level ' . $building_stock_level);
    $updateBuildingStock['stock'] = $building_stock_level;

    debug('Ticks: ' . $tick);
    if ($tick) {
        debug($level);
        $l = 1;
        for ($i = 1; $i <= 20; $i++) {
            if ($level[$i] > $l) {
                $l = $i;
            }
        }
        debug('Level estimate is ' . $l);
        if ($l > $b->level) {
            $updateBuildingStock['level'] = $l;
        }
    }

    DB::building_stock_update(id: $loc, params: $updateBuildingStock, universe: $uni);
}

DB::building_update(id: $loc, params: $updateBuilding, universe: $uni);
