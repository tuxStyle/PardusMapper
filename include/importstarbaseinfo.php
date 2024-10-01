<?php
declare(strict_types=1);

use Pardusmapper\Coordinates;
use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;
use Pardusmapper\CORS;
use Pardusmapper\Request;
use Pardusmapper\DB;

// Enable CORS for any subdomain of pardus.at
CORS::pardus_extended();

require_once('../app/settings.php');

if ($debug) {
    xp($_REQUEST);
    echo '<br>';
}

$db = MySqlDB::instance();

// Set Univers Variable and Session Name
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

// Get Version
$minVersion = 5.8;
$version = Request::version();
http_response($version < $minVersion, ApiResponse::BADREQUEST, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));

// Starbase Main Page Variables
$loc = Request::loc();
http_response(is_null($loc), ApiResponse::BADREQUEST, sprintf('location(loc) query parameter is required or invalid: %s', $loc ?? 'null'));

$name = Request::name();
$image = Request::img();
$faction = Request::faction();
$owner = Request::owner();
$alliance = Request::alliance();
$pop = Request::pop();
$crime = Request::crime();

// Trade Page Variables (Additional)
$credit = Request::credit();

// Starbase Building Page Variables (additional)
$x = Request::x();
$y = Request::y();
$condition = Request::condition();

// Extra request data
$sb = Request::sb();    // visited SB
$sbb = Request::sbb();    // visited SB building
$sbt = Request::sbt();  // SB trade data
$fs = Request::fs(default: 0);    // free space
$squads = Request::squads();  // SB squadrons


// Get Map information
$m = DB::map(id: $loc, universe: $uni);
// Handle query error
if (false === $m && $debug) {
    echo "Query failed: " . MySqlDB::instance()->getDb()->error;
}
// Stop of map not ofund
http_response(is_null($m), ApiResponse::BADREQUEST, sprintf('map not found for location: %s', $loc));

if ($debug) {
    xp([$m]);
    echo '<br>Got Map Data<br>';
}

// Verify Building is already in DB Tables Add if Not
// Perform the query to fetch building information
$b = DB::building(id: $loc, universe: $uni);
if ($b) {
    // Building in DB, Verify Stock is in DB
    $db->execute(sprintf('SELECT * FROM %s_New_Stock WHERE id = ?', $uni), [
        's', $loc
    ]);

    if ($db->numRows() < 1) {
        $db->addBuildingStock($uni, $m->fg, $loc);
    }
} else {
    // Building not in DB
    $db->addBuilding($uni, $m->fg, $loc, 0);

    // After we add a buildimg, load the object
    $b = DB::building(id: $loc, universe: $uni);
}

if ($debug) {
    print_r($b);
}

if ($debug) {
    echo '<br>Got Building Info<br>';
}
// Get Sector and Cluster Information from Location
$s = DB::sector(id: $loc);
http_response(is_null($s), ApiResponse::BADREQUEST, sprintf('sector not found for location: %s', $loc));
if ($debug) {
    echo 'Got Sector Info<br>';
}

$c = DB::cluster(id: $s->c_id);
http_response(is_null($s->c_id), ApiResponse::BADREQUEST, sprintf('cluster not found from sector location: %s', $s->c_id));
if ($debug) {
    echo 'Got Cluster Info<br>';
}

// Double Check that Cluster and Sector have been Set for the Building
if (is_null($b->cluster) || is_null($b->sector)) {
    $db->execute(sprintf('UPDATE %s_Buildings SET `cluster` = ?, `sector` = ? WHERE id = ?', $uni), [
        'ssi', $c->name, $s->name, $loc
    ]);
}

if ($sb) {
    //Visited Starbase
    if ($debug) {
        echo 'Visited Starbase<br>';
    }
    // Collect Info

    // Update DB with common SB info
    if (!$b->x && !$b->y) {
        $x = Coordinates::getX($loc, $s->s_id, $s->rows);
        $y = Coordinates::getY($loc, $s->s_id, $s->rows, $x);

        $db->execute(sprintf('UPDATE %s_Buildings SET `x` = ?, `y` = ? WHERE id = ?', $uni), [
            'iii', $x, $y, $loc
        ]);
    }

    if (isset($faction)) {
        if ($debug) {
            echo 'Updating Faction<br>';
        }

        $db->execute(sprintf('UPDATE %s_Buildings SET `faction` = ? WHERE id = ?', $uni), [
            'si', $faction, $loc
        ]);
    } else {
        if ($debug) {
            echo 'Nulling Faction<br>';
        }

        $db->execute(sprintf('UPDATE %s_Buildings SET `faction` = null WHERE id = ?', $uni), [
            'i', $loc
        ]);
    }
    if (isset($owner)) {
        if ($debug) {
            echo 'Updating Owner<br>';
        }

        $db->execute(sprintf('UPDATE %s_Buildings SET `owner` = ? WHERE id = ?', $uni), [
            'si', $owner, $loc
        ]);
    }
    if (isset($alliance)) {
        if ($debug) {
            echo 'Updating Alliance<br>';
        }

        $db->execute(sprintf('UPDATE %s_Buildings SET `alliance` = ? WHERE id = ?', $uni), [
            'si', $alliance, $loc
        ]);
    } else {
        if ($debug) {
            echo 'Nulling Alliance<br>';
        }

        $db->execute(sprintf('UPDATE %s_Buildings SET `alliance` = null WHERE id = ?', $uni), [
            'i', $loc
        ]);
    }

    // moved this at the end to have the correct timestamp
    $db->execute(sprintf('UPDATE %s_Buildings SET `name` = ?, `image` = ?, `population` = ?, `crime` = ?, updated`= UTC_TIMESTAMP()  WHERE id = ?', $uni), [
        'ssisi', $name, $image, $pop, $crime, $loc
    ]);
}

if (count($sbt) > 0) {
    //Visited a Starbase
    if ($debug) {
        echo 'Visited a Starbase Trade<br>';
    }
    //Collect Info
    if ($fs > 0) {
        $cap = $fs;

        $db->execute(sprintf('UPDATE %s_Buildings SET `freespace` = ? WHERE id = ?', $uni), [
            'ii', $fs, $loc
        ]);
    } else {
        $cap = 0;
    }

    // Loop through all sbt data
    for ($i = 1; $i < count($sbt); $i++) {
        $temp = explode(',', $sbt[$i]);
        if ($debug) {
            print_r($temp);
            echo '<br>';
        }
        $temp[1] = str_replace(',', '', $temp[1]); // Remove commas from the second element
        $cap += $temp[1];
        $name = 'starbase';
        $res = $temp[0];
        
        // Execute the query
        $u = $db->execute('SELECT * FROM Pardus_Upkeep_Data WHERE name = ? AND res = ?', [
            'ss', $name, $res
        ]);

        $building_stock_level = 0;
        $building_stock_max = 0;
        if ($u !== null && isset($u->upkeep)) {
            if ($u->upkeep) {
                $building_stock_level += $temp[1];
                $building_stock_max += $temp[4];
            }
        }
        $stock = 0;
        if ($temp[4]) {
            $stock = round(($temp[1] / $temp[4]) * 100, 0);
            if ($stock > 100) {
                $stock = 100;
            }
        }

        if ($debug) {
            echo 'Stocking for ' . $temp[0] . ' = ' . $stock . '<br>';
        }

        $db->execute(sprintf('SELECT * FROM %s_New_Stock` WHERE name = ? AND id = ?', $uni), [
            'ss', $temp[0], $loc
        ]);
        
        if ($db->numRows() < 1) {            
            $db->execute(sprintf('INSERT INTO `%s_New_Stock (name, id) VALUES (?, ?)', $uni), [
                'si', $temp[0], $loc
            ]);
        }

        $u = $db->execute(
            sprintf('UPDATE `%s_New_Stock 
                        SET
                            `amount` = ?, 
                            `bal` = ?, 
                            `min` = ?, 
                            `max` = ?, 
                            `buy` = ?, 
                            `sell` = ?, 
                            `stock` = ?
                        WHERE name = ? AND id = ?', $uni), [
                            'iiiiiiisi', $temp[1], $temp[2], $temp[3], $temp[4], $temp[5], $temp[6], $stock, $temp[0], $loc
            ]);
        }

    $db->execute(sprintf('UPDATE %s_Buildings SET `capacity` = ?, `credit` = ?  WHERE id = ?', $uni), [
        'iii', $cap, $credit, $loc
    ]);

    // Set Building Stock level
    if ($building_stock_max) {
        $building_stock_level = round(($building_stock_level / $building_stock_max) * 100, 0);
        if ($building_stock_level > 100) {
            $building_stock_level = 100;
        }
    }

    if ($debug) {
        echo 'Building Stock Level ' . $building_stock_level . '<br>';
    }

    $db->execute(sprintf('UPDATE %s_Buildings SET `stock` = ?, stock_updated = UTC_TIMESTAMP()  WHERE id = ?', $uni), [
        'ii', $building_stock_level, $loc
    ]);
}

if (count($squads) > 0) {
    //Visted Squadrons at a Player SB

    // Erase old Squad info from DB.
    $db->execute(sprintf('DELETE FROM %s_Squadrons WHERE id = ?', $uni), [
        'i', $loc
    ]);

    for ($i = 0; $i < sizeOf($squads); $i++) {
        $temp = explode(',', $squads[$i]);

        $db->execute(sprintf('INSERT INTO %s_Squadrons (`image`,`type`,`weapons`,`credit`,`date`,`id`) VALUES (?, ?, ?, ?, UTC_TIMESTAMP(), ?)', $uni), [
            'ssiii', $temp[0], $temp[1], $temp[2], $temp[3], $loc
        ]);
    }

    $db->execute(sprintf('UPDATE %s_Squadrons SET cluster = ?, sector = ?, x = ?, y = ? WHERE id = ?', $uni), [
        'ssiii', $c->name, $s->name, $b->x, $b->y, $loc
    ]);
}

if ($sbb) {
    //Visited SB Building
    if ($debug) {
        echo 'Visited SB Building<br>';
    }
    //Collect Info

    $db->execute(sprintf('UPDATE %s_Buildings SET `image` = ?, `name` = ?, `condition` = ? WHERE id = ?', $uni), [
        'ssii', $image, $name, $condition, $loc
    ]);

    if (isset($faction)) {
        if ($debug) {
            echo 'Updating Faction<br>';
        }
        $db->execute(sprintf('UPDATE %s_Buildings SET `faction` = ? WHERE id = ?', $uni), [
            'si', $faction, $loc
        ]);
    } else {
        if ($debug) {
            echo 'Nulling Faction<br>';
        }
        $db->execute(sprintf('UPDATE %s_Buildings SET `faction` = null WHERE id = ?', $uni), [
            'i', $loc
        ]);
    }
    if (isset($owner)) {
        if ($debug) {
            echo 'Updating Owner<br>';
        }
        $db->execute(sprintf('UPDATE %s_Buildings SET `owner` = ? WHERE id = ?', $uni), [
            'si', $owner, $loc
        ]);
    }
    if (isset($alliance)) {
        if ($debug) {
            echo 'Updating Alliance<br>';
        }
        $db->execute(sprintf('UPDATE %s_Buildings SET `alliance` = ? WHERE id = ?', $uni), [
            'si', $alliance, $loc
        ]);
    } else {
        if ($debug) {
            echo 'Nulling Alliance<br>';
        }
        $db->execute(sprintf('UPDATE %s_Buildings SET `alliance` = null WHERE id = ?', $uni), [
            'i', $loc
        ]);
    }
    //echo ('UPDATE `' . $uni . '_Buildings` SET `updated` = UTC_TIMESTAMP() WHERE id = ' . $loc);
    $db->execute(sprintf('UPDATE %s_Buildings SET `updated` = UTC_TIMESTAMP() WHERE id = ?', $uni), [
        'i', $loc
    ]);
}
