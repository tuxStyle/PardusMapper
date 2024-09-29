<?php
declare(strict_types=1);

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;

// Enable CORS for any subdomain of pardus.at
if (isset($_SERVER['HTTP_ORIGIN']) && preg_match('/^https?:\/\/(orion|artemis|pardus)?\.pardus\.at$/i', (string) $_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);  // Dynamically allow the origin
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');         // Allow the necessary methods
    header('Access-Control-Allow-Headers: Content-Type');               // Allow custom headers (if necessary)
    header('Access-Control-Allow-Credentials: true');                   // Allow cookies (if necessary)
} else {
    http_response(true, ApiResponse::FORBIDDEN, 'CORS policy does not allow access from this origin.');
}

// Handle OPTIONS requests for CORS preflight (important for complex requests)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); // Return 200 OK for preflight requests
}

require_once('../app/settings.php');

$dbClass = new MySqlDB(); // Create an instance of the Database class
$db = $dbClass->getDb();  // Get the mysqli connection object

$debug = true;
if (!isset($_REQUEST['debug'])) {
    $debug = false;
}

// Set Univers Variable and Session Name
if (!isset($_REQUEST['uni'])) {
    exit;
}



if (isset($_REQUEST['uni'])) {
    $stmt = $dbClass->prepare('SELECT ?');
    $stmt->bind_param('s', $_REQUEST['uni']);
    $stmt->execute();
    $stmt->bind_result($uni);
    $stmt->fetch();
    $stmt->close();
} else {
    exit;
}


if ($debug) {
    echo 'Universe = ' . $uni . '<br>';
}

// Get Version
$version = 0;
if (isset($_REQUEST['version'])) {

    if (isset($_REQUEST['version'])) {
        $stmt = $dbClass->prepare('SELECT ?');
        $stmt->bind_param('s', $_REQUEST['version']);
        $stmt->execute();
        $stmt->bind_result($version);
        $stmt->fetch();
        $stmt->close();
    } else {
        exit;
    }
}

if ($version < 5.8) {
    exit;
}

if ($debug) {
    print_r($_REQUEST);
    echo '<br>';
}

// Starbase Main Page Variables
if (isset($_REQUEST['loc'])) {

    if (isset($_REQUEST['loc'])) {
        $stmt = $dbClass->prepare('SELECT ?');
        $stmt->bind_param('s', $_REQUEST['loc']);
        $stmt->execute();
        $stmt->bind_result($loc);
        $stmt->fetch();
        $stmt->close();
    } else {
        exit;
    }
} else {
    exit;
}
if (isset($_REQUEST['name'])) {

    if (isset($_REQUEST['name'])) {
        $stmt = $dbClass->prepare('SELECT ?');
        $stmt->bind_param('s', $_REQUEST['name']);
        $stmt->execute();
        $stmt->bind_result($name);
        $stmt->fetch();
        $stmt->close();
    }
}
if (isset($_REQUEST['img'])) {

    if (isset($_REQUEST['img'])) {
        $stmt = $dbClass->prepare('SELECT ?');
        $stmt->bind_param('s', $_REQUEST['img']);
        $stmt->execute();
        $stmt->bind_result($image);
        $stmt->fetch();
        $stmt->close();
    }
}
if (isset($_REQUEST['faction'])) {

    if (isset($_REQUEST['faction'])) {
        $stmt = $dbClass->prepare('SELECT ?');
        $stmt->bind_param('s', $_REQUEST['faction']);
        $stmt->execute();
        $stmt->bind_result($faction);
        $stmt->fetch();
        $stmt->close();
    }
}
if (isset($_REQUEST['owner'])) {

    if (isset($_REQUEST['owner'])) {
        $stmt = $dbClass->prepare('SELECT ?');
        $stmt->bind_param('s', $_REQUEST['owner']);
        $stmt->execute();
        $stmt->bind_result($owner);
        $stmt->fetch();
        $stmt->close();
    }
}
if (isset($_REQUEST['alliance'])) {
    $alliance = $dbClass->real_escape_string($_REQUEST['alliance']);
}
if (isset($_REQUEST['pop'])) {
    $pop = $dbClass->real_escape_string($_REQUEST['pop']);
}
if (isset($_REQUEST['crime'])) {
    $crime = $dbClass->real_escape_string($_REQUEST['crime']);
}

// Trade Page Variables (Additional)
if (isset($_REQUEST['credit'])) {
    $credit = $dbClass->real_escape_string($_REQUEST['credit']);
}

// Starbase Building Page Variables (additional)
if (isset($_REQUEST['x'])) {
    $x = $dbClass->real_escape_string($_REQUEST['x']);
}
if (isset($_REQUEST['y'])) {
    $y = $dbClass->real_escape_string($_REQUEST['y']);
}
if (isset($_REQUEST['condition'])) {
    $condition = $dbClass->real_escape_string($_REQUEST['condition']);
}

if ($debug) {
    echo 'Location = ' . $loc . '<br>';
}

// Get Map information
$m = null;
$result = $dbClass->query('SELECT * FROM ' . $uni . '_Maps WHERE id = ' . $loc);
if ($result) {
    $m = $result->fetch_object();
} else {
    // Handle query error
    if ($debug) {
        echo "Query failed: " . $db->error;
    }
}

if(is_null($m)) {
    exit(sprintf('map(%s) not found', $loc));
}
if ($debug) {
    print_r($m);
}
if ($debug) {
    echo '<br>Got Map Data<br>';
}

// Verify Building is already in DB Tables Add if Not
// Perform the query to fetch building information
$result = $dbClass->query('SELECT * FROM ' . $uni . '_Buildings WHERE id = ' . $loc);
if ($b = $result->fetch_object()) {  // Use fetch_object() on the result set
    // Building in DB, Verify Stock is in DB
    $result_stock = $dbClass->query('SELECT * FROM ' . $uni . '_New_Stock WHERE id = ' . $loc);
    if ($result_stock->num_rows < 1) {
        $dbClass->addBuildingStock($uni, $m->fg, $loc);
    }
} else {
    // Building not in DB
    $dbClass->addBuilding($uni, $m->fg, $loc, 0);
    $result = $dbClass->query('SELECT * FROM ' . $uni . '_Buildings WHERE id = ' . $loc);
    $b = $result->fetch_object();  // Use fetch_object() on the result set
}



$result = $dbClass->query('SELECT * FROM ' . $uni . '_Buildings WHERE id = ' . $loc);
$b = $result->fetch_object();

if ($debug) {
    print_r($b);
}

if ($debug) {
    echo '<br>Got Building Info<br>';
}
// Get Sector and Cluster Information from Location
$s = $dbClass->getSector($loc, "");
if ($debug) {
    echo 'Got Sector Info<br>';
}
$c = $dbClass->getCluster($s->c_id, "");
if ($debug) {
    echo 'Got Cluster Info<br>';
}

// Double Check that Cluster and Sector have been Set for the Building
if (is_null($b->cluster)) {
    $dbClass->query('UPDATE ' . $uni . '_Buildings SET cluster = \'' . $c->name . '\' WHERE id = ' . $loc);
}
if (is_null($b->sector)) {
    $dbClass->query('UPDATE ' . $uni . '_Buildings SET sector = \'' . $s->name . '\' WHERE id = ' . $loc);
}

if (isset($_REQUEST['sb'])) {
    //Visited Starbase
    if ($debug) {
        echo 'Visited Starbase<br>';
    }
    // Collect Info

    // Update DB with common SB info
    if (!$b->x && !$b->y) {
        $x = $dbClass->getX($loc, $s->s_id, $s->rows);
        $y = $dbClass->getY($loc, $s->s_id, $s->rows, $x);
        $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `x` = ' . $x . ', `y`= ' . $y . ' WHERE id = ' . $loc);
    }
    $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `name`= \'' . $name . '\', `image`= \'' . $image . '\', `population`= ' . $pop . ', `crime`= \'' . $crime . '\', `updated`= UTC_TIMESTAMP() WHERE id = ' . $loc);
    if (isset($_REQUEST['faction'])) {
        if ($debug) {
            echo 'Updating Faction<br>';
        }
        $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `faction`= \'' . $faction . '\' WHERE id = ' . $loc);
    } else {
        if ($debug) {
            echo 'Nulling Faction<br>';
        }
        $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `faction`= null WHERE id = ' . $loc);
    }
    if (isset($_REQUEST['owner'])) {
        if ($debug) {
            echo 'Updating Owner<br>';
        }
        $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `owner`= \'' . $owner . '\' WHERE id = ' . $loc);
    }
    if (isset($_REQUEST['alliance'])) {
        if ($debug) {
            echo 'Updating Alliance<br>';
        }
        $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `alliance`= \'' . $alliance . '\' WHERE id = ' . $loc);
    } else {
        if ($debug) {
            echo 'Nulling Alliance<br>';
        }
        $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `alliance`= null WHERE id = ' . $loc);
    }
}

if (isset($_REQUEST['sbt'])) {
    //Visited a Starbase
    if ($debug) {
        echo 'Visited a Starbase<br>';
    }
    //Collect Info
    if (isset($_REQUEST['fs'])) {
        $fs = $dbClass->real_escape_string($_REQUEST['fs']);
        $cap = $fs;
        $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `freespace`= ' . $fs . ' WHERE id = ' . $loc);
    } else {
        $cap = 0;
    }
    $sbt = explode('~', (string) $dbClass->real_escape_string($_REQUEST['sbt']));
    if ($debug) {
        print_r($sbt);
    }
    if ($debug) {
        echo '<br>';
    }
    // Loop through all sbt data
    for ($i = 1; $i < sizeof($sbt); $i++) {
        $temp = explode(',', $sbt[$i]);
        if ($debug) {
            print_r($temp);
        }
        if ($debug) {
            echo '<br>';
        }
        $temp[1] = str_replace(',', '', $temp[1]); // Remove commas from the second element
        $cap += $temp[1];
        $stmt = $dbClass->prepare('SELECT * FROM Pardus_Upkeep_Data WHERE name = ? AND res = ?');
        $stmt->bind_param('ss', $name, $res);

        $name = 'starbase';
        $res = $temp[0];

        // Execute the query
        $stmt->execute();
        $result = $stmt->get_result();
        $u = $result->fetch_object(); // Fetch the object
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
        $dbClass->query('SELECT * FROM `' . $uni . '_New_Stock` WHERE name = \'' . $temp[0] . '\' AND id = ' . $loc);
        if ($dbClass->numRows() < 1) {
            $dbClass->query('INSERT INTO ' . $uni . '_New_Stock (id,name) VALUES (' . $loc . ',\'' . $temp[0] . '\')');
        }
        $dbClass->query('UPDATE `' . $uni . '_New_Stock` SET `amount` = ' . $temp[1] . ' WHERE name = \'' . $temp[0] . '\' AND id = ' . $loc);
        $dbClass->query('UPDATE `' . $uni . '_New_Stock` SET `bal` = ' . $temp[2] . ' WHERE name = \'' . $temp[0] . '\' AND id = ' . $loc);
        $dbClass->query('UPDATE `' . $uni . '_New_Stock` SET `min` = ' . $temp[3] . ' WHERE name = \'' . $temp[0] . '\' AND id = ' . $loc);
        $dbClass->query('UPDATE `' . $uni . '_New_Stock` SET `max` = ' . $temp[4] . ' WHERE name = \'' . $temp[0] . '\' AND id = ' . $loc);
        $dbClass->query('UPDATE `' . $uni . '_New_Stock` SET `buy` = ' . $temp[5] . ' WHERE name = \'' . $temp[0] . '\' AND id = ' . $loc);
        $dbClass->query('UPDATE `' . $uni . '_New_Stock` SET `sell` = ' . $temp[6] . ' WHERE name = \'' . $temp[0] . '\' AND id = ' . $loc);
        $dbClass->query('UPDATE `' . $uni . '_New_Stock` SET `stock` = ' . $stock . ' WHERE name = \'' . $temp[0] . '\' AND id = ' . $loc);
    }

    $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `capacity`= ' . $cap . ', `credit`= ' . $credit . ' WHERE id = ' . $loc);
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
    //echo ('UPDATE `' . $uni . '_Buildings` SET `updated` = UTC_TIMESTAMP() WHERE id = ' . $loc);
    $dbClass->query('UPDATE ' . $uni . '_Buildings SET stock = ' . $building_stock_level . ', stock_updated = UTC_TIMESTAMP() WHERE id = ' . $loc);
}

if (isset($_REQUEST['squads'])) {
    //Visted Squadrons at a Player SB
    if ($debug) {
        echo 'Visited Squadrons<br>';
    }
    // Erase old Squad info from DB.
    $dbClass->query('DELETE FROM `' . $uni . '_Squadrons` WHERE id = ' . $loc);
    //Collect Info
    $squads = explode('~', (string) $dbClass->real_escape_string($_REQUEST['squads']));

    for ($i = 0; $i < sizeOf($squads); $i++) {
        $temp = explode(',', $squads[$i]);
        $dbClass->query('INSERT INTO `' . $uni . '_Squadrons` (`id`,`image`,`type`,`weapons`,`credit`,`date`) VALUES (' . $loc . ',\'' . $temp[0] . '\',\'' . $temp[1] . '\',' . $temp[2] . ',' . $temp[3] . ', UTC_TIMESTAMP())');
    }
    $dbClass->query('UPDATE ' . $uni . '_Squadrons SET cluster = \'' . $c->name . '\' WHERE id = ' . $loc);
    $dbClass->query('UPDATE ' . $uni . '_Squadrons SET sector = \'' . $s->name . '\' WHERE id = ' . $loc);
    $dbClass->query('UPDATE ' . $uni . '_Squadrons SET x = ' . $b->x . ' WHERE id = ' . $loc);
    $dbClass->query('UPDATE ' . $uni . '_Squadrons SET y = ' . $b->y . ' WHERE id = ' . $loc);
}

if (isset($_REQUEST['sbb'])) {
    //Visited SB Building
    if ($debug) {
        echo 'Visited SB Building<br>';
    }
    //Collect Info

    $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `image`= \'' . $image . '\',`name`= \'' . $name . '\',`condition`= ' . $condition . ' WHERE id = ' . $loc);
    if (isset($_REQUEST['faction'])) {
        if ($debug) {
            echo 'Updating Faction<br>';
        }
        $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `faction`= \'' . $faction . '\' WHERE id = ' . $loc);
    } else {
        if ($debug) {
            echo 'Nulling Faction<br>';
        }
        $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `faction`= null WHERE id = ' . $loc);
    }
    if (isset($_REQUEST['owner'])) {
        if ($debug) {
            echo 'Updating Owner<br>';
        }
        $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `owner`= \'' . $owner . '\' WHERE id = ' . $loc);
    }
    if (isset($_REQUEST['alliance'])) {
        if ($debug) {
            echo 'Updating Alliance<br>';
        }
        $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `alliance`= \'' . $alliance . '\' WHERE id = ' . $loc);
    } else {
        if ($debug) {
            echo 'Nulling Alliance<br>';
        }
        $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `alliance`= null WHERE id = ' . $loc);
    }
    //echo ('UPDATE `' . $uni . '_Buildings` SET `updated` = UTC_TIMESTAMP() WHERE id = ' . $loc);
    $dbClass->query('UPDATE `' . $uni . '_Buildings` SET `updated` = UTC_TIMESTAMP() WHERE id = ' . $loc);
}

$db->close();
