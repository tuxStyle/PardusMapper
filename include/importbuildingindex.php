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
$version = Request::version();
http_response($version < $minVersion, ApiResponse::BADREQUEST, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));

$sector = Request::pstring(key: 'sector');
http_response(is_null($sector), ApiResponse::BADREQUEST, sprintf('sector query parameter is required or invalid: %s', $sector ?? 'null'));

$date = Request::pstring(key: 'date');
http_response(is_null($date), ApiResponse::BADREQUEST, sprintf('date query parameter is required or invalid: %s', $date ?? 'null'));

$x = Request::x(key: 'x');
$y = Request::y(key: 'y');
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
http_response(strtotime($date) < strtotime($b->stock_updated), ApiResponse::BADREQUEST, sprintf('Index Date: %s Older the Stocking Date: %s', strtotime($date), strtotime($b->stock_updated)));

http_response(!isset($_REQUEST['bi']), ApiResponse::OK, sprintf('bi query parameter is required: %s', $_REQUEST['bi'] ?? 'null'));

// Get Cluster Information from Location
$c = DB::cluster(id: $s->c_id);

// Double Check that Cluster and Sector have been Set for the Building
if (is_null($b->cluster)) {
    DB::building_update_cluster(id: $loc, cluster: $c->name, universe: $uni);
}
if (is_null($b->sector)) {
    DB::building_update_sector(id: $loc, sector: $s->name, universe: $uni);
}

debug('Visited Building Index for the Sector');

if (is_null($b->x) && is_null($b->y)) {
    DB::building_update_xy(id: $loc, x: $x, y: $y, universe: $uni);
}

$db->execute(sprintf('UPDATE %s_Buildings SET `image`= ?, `name`= ?, `owner`= ? WHERE id = ?', $uni), [
    'sssi', $image, $name, $owner, $loc
]);

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
            $db->execute(sprintf('INSERT INTO %s_New_Stock (name, id) VALUES (?, ?)', $uni), [
                'si', $r->name, $loc
            ]);
        }
        $db->execute(sprintf('UPDATE %s_New_Stock SET amount = ? WHERE name = ? AND id = ?', $uni), [
            'isi', $s[1], $r->name, $loc
        ]);
        $db->execute(sprintf('UPDATE %s_New_Stock SET buy = ? WHERE name = ? AND id = ?', $uni), [
            'isi', $s[2], $r->name, $loc
        ]);
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
            $db->execute(sprintf('INSERT INTO %s_New_Stock (name, id) VALUES (?, ?)', $uni), [
                'si', $r->name, $loc
            ]);
        }

        $q = DB::building_stock(id: $loc, name: $r->name, universe: $uni);
        if ($q->max > 0) {
            $amount = $q->max - $b[1];
            debug('Amount : ' . $amount);
            $db->execute(sprintf('UPDATE %s_New_Stock SET amount = ? WHERE name = ? AND id = ?', $uni), [
                'isi', $amount, $r->name, $loc
            ]);
        }
        $db->execute(sprintf('UPDATE %s_New_Stock SET sell = ? WHERE name = ? AND id = ?', $uni), [
            'isi', $b[2], $r->name, $loc
        ]);
    }
}

if (isset($fs)) {
    $db->execute(sprintf('UPDATE %s_Buildings SET `freespace` = ? WHERE id = ?', $uni), [
        'ii', $fs, $loc
    ]);
}
$db->execute(sprintf('UPDATE %s_Buildings', $uni) . ' SET  `stock_updated`= STR_TO_DATE(?, \'%a %b %e %T GMT %Y\') WHERE id = ?', [
    'si', $date, $loc
]);

debug('Finished updating stocks');

$db->close();
