<?php
declare(strict_types=1);

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
http_response(is_null($uni), ApiResponse::OK, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

// Get Version
$minVersion = 5.8;
$version = Request::pfloat(key: 'version', default: 0);
http_response($version < $minVersion, ApiResponse::OK, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));

$bo = Request::pint(key: 'bo');
http_response(is_null($bo), ApiResponse::OK, sprintf('bo query parameter is required: %s', $bo ?? 'null'));

$owner = Request::pstring(key: 'owner');
$faction = Request::pstring(key: 'faction');

for ($i = 1; $i < $bo; $i++) {
    $rb = Request::pstring(key: 'b' . $i);
    if (is_null($rb)) {
        continue;
    }

    $b = explode(",", $rb);
    debug($b);

    $loc = (int)$b[0];
    $name = $b[1];
    $sector = $b[2];
    $x = (int)$b[3];
    $y = (int)$b[4];
    $condition = (int)$b[5];
    $level = $name == 'Trading Outpost' ? 0 : (int)$b[6];

    $b_data = DB::building_static(name: $name);
    $image = $b_data->image;
    $bldg = DB::building(id: $loc, universe: $uni);

    if (is_null($bldg)) {
        debug('Adding New Building ' . $name);
        DB::building_add(universe: $uni, image: $image, id: $loc, sb: 0);
    }

    debug('Getting Sector and Cluster Information');
    $s = DB::sector(sector: $sector);
    $c = DB::cluster(id: $s->c_id);

    $updateBuilding = [];

    debug('Updating Cluster and Sector Info');
    $updateBuilding['cluster'] = $c->name;
    $updateBuilding['sector'] = $sector;

    debug('Updating X and Y');
    $updateBuilding['x'] = $x;
    $updateBuilding['y'] = $y;

    debug('Updating Name and Image');
    $updateBuilding['name'] = $name;
    $updateBuilding['image'] = $image;

    debug('Updating Owner and Faction');
    $updateBuilding['owner'] = $owner;
    $updateBuilding['faction'] = !is_null($faction) ? $faction : null;

    debug('Updating Condition and Level');
    $updateBuilding['condition'] = $condition;
    $updateBuilding['level'] = $level;

    DB::building_update(id: $loc, params: $updateBuilding, universe: $uni);

    $ru = Request::pstring(key: 'u' . $i);
    $u = explode("~", (string) $ru);
    debug($u);

    for ($x = 1; $x < count($u); $x++) {
        $temp = explode(",", $u[$x]);
        debug($temp);

        $r = DB::building_stock(id: $loc, name: $temp[0], universe: $uni);
        if (is_null($r)) {
            DB::stock_create(id: $loc, name: $temp[0], universe: $uni);
            $r = DB::building_stock(id: $loc, name: $temp[0], universe: $uni);
        }

        $updateStock = [];
        $updateStock['amount'] = (int)$temp[1];
        if ($r->max > 0) {
            $stock = round(($temp[1] / $r->max) * 100, 0);
            if ($stock > 100) {
                $stock = 100;
            }

            $updateStock['stock'] = (int)$stock;
        }

        DB::stock_update(id: $loc, name: $temp[0], params: $updateStock, universe: $uni);
    }

    $rp = Request::pstring(key: 'p' . $i);
    if (isset($rp)) {
        $p = explode("~", $rp);
        debug($p);

        for ($x = 1; $x < count($p); $x++) {
            $temp = explode(",", $p[$x]);
            debug($temp);

            $updateStock = [];
            $updateStock['amount'] = (int)$temp[1];
            DB::stock_update(id: $loc, name: $temp[0], params: $updateStock, universe: $uni);
        }
    }

    debug('Finished Updating Stock Info');
    DB::building_stock_update(id: $loc, params: [], universe: $uni); //just set stock updated timestamp
}
