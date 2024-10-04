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
$version = Request::version();
http_response($version < $minVersion, ApiResponse::BADREQUEST, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));


// Building Main Page Variables
$loc = Request::pint(key: 'loc');
http_response(is_null($loc), ApiResponse::BADREQUEST, sprintf('loc query parameter is required or invalid: %s', $loc ?? 'null'));

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


$db = MySqlDB::instance();  // Create an instance of the Database class


// Get Map information
$m = DB::map(id: $loc, universe: $uni);

// Verify Building is already in DB Tables Add if Not
$b = DB::building(id: $loc, universe: $uni);
if ($b) {
	// Building in DB - Verify Stock is in DB
	if (!str_contains("sb_", $m->fg)) {  // Correct usage of strpos
		debug('Checking Stocking Info');

        $stocks = DB::stocks(id: $loc, universe: $uni);
		if (0 === count($stocks)) {
			$db->addBuildingStock($uni, $m->fg, $loc);
		}
	}
} else {
	// Building not in DB
	$db->addBuilding($uni, $m->fg, $loc, 0);
    $b = DB::building(id: $loc, universe: $uni);
}

debug($b);

if (str_contains("sb_", $m->fg)) {
	debug('We are Flying Close to a SB');

	$db->execute('SELECT * FROM Pardus_Buildings where starbase < ? ORDER BY starbase DESC LIMIT 1', [
        'i', $loc
    ]);
	$q = $db->nextObject();
	$x = Coordinates::getX($loc, $q->starbase, 13);
	$y = Coordinates::getY($loc, $q->starbase, 13, $x);
	$s = DB::sector(id: $q->id);
} else {
    debug('Get location information for location: ' . $loc);

	// Get Sector and Cluster Information from Location
	$db->execute('SELECT * FROM Pardus_Sectors WHERE s_id < ? ORDER BY s_id DESC LIMIT 1', [
        'i', $loc
    ]);
	$s = DB::sector(id: $loc);
	$x = Coordinates::getX($loc, $s->s_id, $s->rows);
	$y = Coordinates::getY($loc, $s->s_id, $s->rows, $x);
}
$c = DB::cluster(id: $s->c_id);

// Double Check that Cluster and Sector have been Set for the Building
if (is_null($b->cluster)) {
    DB::building_update_cluster(id: $loc, cluster: $c->name, universe: $uni);
}
if (is_null($b->sector)) {
    DB::building_update_sector(id: $loc, sector: $s->name, universe: $uni);
}
if (!$b->x && !$b->y) {
    DB::building_update_xy(id: $loc, x: $x, y: $y, universe: $uni);
}

if (isset($_REQUEST['building'])) {
	// Visited a Building
	debug('Visited a Building');

	// Collect Info
	$db->execute(sprintf('UPDATE %s_Buildings SET `image`= ?, `name`= ?, `condition`= ? WHERE id = ?', $uni), [
        'ssii', $image, $name, $condition, $loc
    ]);

	if (isset($owner)) {
		debug('Updating owner');
		$db->execute(sprintf('UPDATE %s_Buildings SET `owner`= ? WHERE id = ?', $uni), [
            'si', $owner, $loc
        ]);
	} else {
        debug('Nulling owner');
		$db->execute(sprintf('UPDATE %s_Buildings SET `owner`= NULL WHERE id = ?', $uni), [
            'i', $loc
        ]);
	}
	if (isset($alliance)) {
        debug('Updating alliance');
		$db->execute(sprintf('UPDATE %s_Buildings SET `alliance`= ? WHERE id = ?', $uni), [
            'si', $alliance, $loc
        ]);
	} else {
        debug('Nulling alliance');
		$db->execute(sprintf('UPDATE %s_Buildings SET `alliance`= NULL WHERE id = ?', $uni), [
            'i', $loc
        ]);
	}
	if (isset($faction)) {
        debug('Updating faction');
		$db->execute(sprintf('UPDATE %s_Buildings SET `faction`= ? WHERE id = ?', $uni), [
            'si', $faction, $loc
        ]);
	} else {
        debug('Nulling faction');
		$db->execute(sprintf('UPDATE %s_Buildings SET `faction`= NULL WHERE id = ?', $uni), [
            'i', $loc
        ]);
	}
	$db->execute(sprintf('UPDATE %s_Buildings SET `updated` = UTC_TIMESTAMP() WHERE id = ?', $uni), [
        'i', $loc
    ]);

	// If we can see the Building then there are no NPCs at this location
	$db->removeNPC($uni, $loc);
}
if (isset($bt)) {
	// Visited Building Trade
    debug('Visited Building Trade');
	// Collect Info
	$db->execute(sprintf('UPDATE %s_Buildings SET `image`= ?, `name`= ? WHERE id = ?', $uni), [
        'ssi', $image, $name, $loc
    ]);

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
		$temp = explode(',', $db->real_escape_string($bt[$i]));
        debug($temp);
		debug($cap);
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


        $db->execute('SELECT * FROM Pardus_Upkeep_Data WHERE name = ? AND res = ?', [
            'ss', $name, $temp[0]
        ]);

		$u = $db->nextObject();
        debug($u);
		$amount = $u->amount;
		$upkeep = $u->upkeep;
		$db->execute(sprintf('SELECT * FROM %s_New_Stock WHERE name = ? AND id = ?', $uni), [
            'si', $temp[0], $loc
        ]);
		if ($tick && $s = $db->nextObject()) {
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
		$db->execute(sprintf('SELECT * FROM %s_New_Stock WHERE name = ? AND id = ?', $uni), [
            'si', $temp[0], $loc
        ]);
		if ($db->numRows() < 1) {
			$db->execute(sprintf('INSERT INTO %s_New_Stock (name, id) VALUES (?, ?)', $uni), [
                'si', $temp[0], $loc
            ]);
		}
		$db->execute(sprintf('UPDATE %s_New_Stock SET `amount` = ? WHERE name = ? AND id = ?', $uni), [
            'isi', $temp[1], $temp[0], $loc
        ]);
		$db->execute(sprintf('UPDATE %s_New_Stock SET `bal` = 0 WHERE name = ? AND id = ?', $uni), [
            'si', $temp[0], $loc
        ]);
		$db->execute(sprintf('UPDATE %s_New_Stock SET `min` = ? WHERE name = ? AND id = ?', $uni), [
            'isi', $temp[1], $temp[2], $loc
        ]);
		$db->execute(sprintf('UPDATE %s_New_Stock SET `max` = ? WHERE name = ? AND id = ?', $uni), [
            'isi', $temp[1], $temp[3], $loc
        ]);
		$db->execute(sprintf('UPDATE %s_New_Stock SET `buy` = ? WHERE name = ? AND id = ?', $uni), [
            'isi', $temp[1], $temp[4], $loc
        ]);
		$db->execute(sprintf('UPDATE %s_New_Stock SET `sell` = ? WHERE name = ? AND id = ?', $uni), [
            'isi', $temp[1], $temp[5], $loc
        ]);
		$db->execute(sprintf('UPDATE %s_New_Stock SET `stock` = ? WHERE name = ? AND id = ?', $uni), [
            'isi', $stock, $temp[0], $loc
        ]);
		//$db->execute(sprintf('UPDATE %s_New_Stock SET `stock_updated`= UTC_TIMESTAMP() where id = ?' . $loc, $uni), [
        //  'i', $loc
        //]);
	}
	$db->execute(sprintf('UPDATE %s_Buildings SET `capacity`= ?, `freespace`= ?, `credit`= ?, `stock_updated`= UTC_TIMESTAMP() WHERE id = ?', $uni), [
        'iiii', $cap, $fs, $credit, $loc
    ]);


	// Set Building Stock level
	if ($building_stock_max) {
		$building_stock_level = round(($building_stock_level / $building_stock_max) * 100, 0);
		if ($building_stock_level > 100) {
			$building_stock_level = 100;
		}
	}

    debug('Building Stock Level ' . $building_stock_level);
	$db->execute(sprintf('UPDATE %s_Buildings SET stock = ? WHERE id = ?', $uni), [
        'ii', $building_stock_level, $loc
    ]);

	// End Test Stock Table
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
			$db->execute(sprintf('UPDATE %s_Buildings SET `level` = ? WHERE id = ?', $uni), [
                'ii', $l, $loc
            ]);
		}
	}
}

$db->close();
