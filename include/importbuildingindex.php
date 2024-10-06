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
$minVersion = 5.7;
$version = Request::pint(key: 'version', default: 0);
http_response($version < $minVersion, ApiResponse::BADREQUEST, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));

$sector = Request::pstring(key: 'sector');
http_response(is_null($sector), ApiResponse::BADREQUEST, sprintf('sector query parameter is required or invalid: %s', $sector ?? 'null'));

$date = Request::pstring(key: 'date');
http_response(is_null($date), ApiResponse::BADREQUEST, sprintf('date query parameter is required or invalid: %s', $date ?? 'null'));

$x = Request::pint(key: 'x');
$y = Request::pint(key: 'y');
$image = Request::pstring(key: 'img');
$name = Request::pstring(key: 'name');
$owner = Request::pstring(key: 'owner');
$bis = Request::pstring(key: 'bis');
$bib = Request::pstring(key: 'bib');
$fs = Request::pstring(key: 'fs');
if (!is_null($fs)) { $fs = str_replace(',', '', $fs); }

$s = DB::sector(sector: $sector);

$loc = Coordinates::getID($s->s_id, $s->rows, $x, $y);
debug('Location : ' . $loc);

$db = MySqlDB::instance();

// Get Map information
$m = DB::map(id: $loc, universe: $uni);
debug($m);

// Verify Building is already in DB Tables Add if Not
$b = DB::building(id: $loc, universe: $uni);
if ($b) {
	// Building in DB Verify Stock is in DB
    debug('Building in DB');

    $stocks = DB::building_stock(id: $loc, universe: $uni);

	if (is_array($stocks) && 0 === count($stocks)) {
        debug('Adding Stock Info');
		$db->addBuildingStock($uni, $image, $loc);
	}
} else {
	// Building not in DB
    debug('Building not in DB Adding');
	$db->addBuilding($uni, $image, $loc, 0);    // it will also add building stock

    $b = DB::building(id: $loc, universe: $uni);
    debug('Got Building Info');
}

// Verify Index Info is newer than DB
debug('Index Date ' . (new DateTime($date))->format('Y-m-d H:i:s') . ' - ' . strtotime($date));
debug('Building Date ' . $b->stock_updated . ' - ' . strtotime($b->stock_updated));
// http_response(strtotime($date) < strtotime($b->stock_updated), ApiResponse::BADREQUEST, sprintf('Index Date: %s Older the Stocking Date: %s', strtotime($date), strtotime($b->stock_updated)));

http_response(!isset($_REQUEST['bi']), ApiResponse::OK, sprintf('bi query parameter is required: %s', $_REQUEST['bi'] ?? 'null'));

// Get Cluster Information from Location
$c = DB::cluster(id: $s->c_id);

$updateBuilding = [];
$updateBuildingStock = [];

// Double Check that Cluster and Sector have been Set for the Building
if (is_null($b->cluster)) {
    $updateBuilding['cluster'] = $c->name;
}
if (is_null($b->sector)) {
    $updateBuilding['sector'] = $s->name;
}

debug('Visited Building Index for the Sector');

if (is_null($b->x) && is_null($b->y)) {
    $updateBuilding['x'] = $x;
    $updateBuilding['y'] = $y;
}

$updateBuilding['image'] = $image;
$updateBuilding['name'] = $name;
$updateBuilding['owner'] = $owner;

if (isset($bis)) {
    debug('Selling Resources');

    $selling = explode('~', $bis);
    debug($selling);

    for ($i = 0; $i < count($selling); $i++) {
        $s = explode(',', $selling[$i]);
        debug($s);
        $r = DB::res_data(image: $s[0]);

        $q = DB::building_stock(id: $loc, name: $r->name, universe: $uni);
        if (is_null($q)) {
            DB::stock_create(id: $loc, name: $r->name, universe: $uni);
        }

        $updateStock = [];
        $updateStock['amount'] = (int)$s[1];
        $updateStock['buy'] = (int)$s[2];
        DB::stock_update(id: $loc, name: $r->name, params: $updateStock, universe: $uni);
    }
}

if (isset($bib)) {
    debug('Buying Resources');

    $buying = explode('~', $bib);
    debug($buying);

    for ($i = 0; $i < count($buying); $i++) {
        $b = explode(',', $buying[$i]);
        debug($b);
        $r = DB::res_data(image: $b[0]);

        $q = DB::building_stock(id: $loc, name: $r->name, universe: $uni);
        if (is_null($q)) {
            DB::stock_create(id: $loc, name: $r->name, universe: $uni);
        }

        $updateStock = [];
        $q = DB::building_stock(id: $loc, name: $r->name, universe: $uni);
        if ($q->max > 0) {
            $amount = $q->max - $b[1];
            debug('Amount : ' . $amount);
            $updateStock['amount'] = (int)$amount;
        }
        $updateStock['buy'] = (int)$b[2];
        DB::stock_update(id: $loc, name: $r->name, params: $updateStock, universe: $uni);
    }
}

if (isset($fs)) {
    $updateBuildingStock['freespace'] = $fs;
    $updateBuildingStock['date'] = $date;
}

DB::building_update(id: $loc, params: $updateBuilding, universe: $uni);
DB::building_stock_update(id: $loc, params: $updateBuildingStock, universe: $uni);

debug('Finished updating stocks');

$db->close();
