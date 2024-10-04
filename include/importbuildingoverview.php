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
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

// Get Version
$minVersion = 5.8;
$version = Request::version();
http_response($version < $minVersion, ApiResponse::BADREQUEST, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));

$bo = Request::pint(key: 'bo');
http_response(is_null($bo), ApiResponse::OK, sprintf('bo query parameter is required: %s', $bo ?? 'null'));

$owner = Request::pstring(key: 'owner');
$faction = Request::pstring(key: 'faction');

$db = MySqlDB::instance();


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
        $db->addBuilding($uni, $image, $loc, 0);
    }

    debug('Getting Sector and Cluster Information');
    $s = DB::sector(sector: $sector);
    $c = DB::cluster(id: $s->c_id);

    $params = [];

    debug('Updating Cluster and Sector Info');
    $params['cluster'] = $c->name;
    $params['sector'] = $sector;

    debug('Updating X and Y');
    $params['x'] = $x;
    $params['y'] = $y;

    debug('Updating Name and Image');
    $params['name'] = $name;
    $params['image'] = $image;

    debug('Updating Owner and Faction');
    $params['owner'] = $owner;
    $params['faction'] = !is_null($faction) ? $faction : null;

    debug('Updating Condition and Level');
    $params['condition'] = $condition;
    $params['level'] = $level;

    DB::building_update(id: $loc, params: $params, universe: $uni);

    $ru = Request::pstring(key: 'u' . $i);
    $u = explode("~", $ru);
    debug($u);

    for ($x = 1; $x < count($u); $x++) {
        $temp = explode(",", $u[$x]);
        debug($temp);

        $r = DB::building_stock(id: $loc, name: $temp[0], universe: $uni);
        if (is_null($r)) {
            $db->execute(sprintf('INSERT INTO %s_New_Stock (name, id) VALUES (?, ?)', $uni), [
                'si', $temp[0], $loc
            ]);
            $r = DB::building_stock(id: $loc, name: $temp[0], universe: $uni);
        }

        $db->execute(sprintf('UPDATE %s_New_Stock SET amount = ? WHERE name = ? AND id = ?', $uni), [
            'isi', $temp[1], $temp[0], $loc
        ]);
        if ($r->max > 0) {
            $stock = round(($temp[1] / $r->max) * 100, 0);
            if ($stock > 100) {
                $stock = 100;
            }

            $db->execute(sprintf('UPDATE %s_New_Stock SET stock = ? WHERE name = ? AND id = ?', $uni), [
                'isi', $stock, $temp[0], $loc
            ]);
        }
    }

    $rp = Request::pstring(key: 'p' . $i);
    if (isset($rp)) {
        $p = explode("~", $rp);
        debug($p);

        for ($x = 1; $x < count($p); $x++) {
            $temp = explode(",", $p[$x]);
            debug($temp);

            $db->execute(sprintf('UPDATE %s_New_Stock SET amount = ? WHERE name = ? AND id = ?', $uni), [
                'isi', $temp[1], $temp[0], $loc
            ]);
        }
    }

    debug('Finished Updating Stock Info');
    $db->execute(sprintf('UPDATE %s_Buildings SET stock_updated = UTC_TIMESTAMP() WHERE id = ?', $uni), [
        'i', $loc
    ]);
}

$db->close();
