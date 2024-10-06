<?php
declare(strict_types=1);

use Pardusmapper\Core\MySqlDB;
use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Request;
use Pardusmapper\DB;
use Pardusmapper\Coordinates;
use Pardusmapper\CORS;

require_once('../app/settings.php');

CORS::pardus();

$db = MySqlDB::instance(); // Create an instance of the Database class

// Test Mission Table

// Set Univers Variable and Session Name
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

// Get Version
$minVersion = 5.8;
$version = Request::pfloat(key: 'version', default: 0);
http_response($version < $minVersion, ApiResponse::BADREQUEST, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));

// Set Location
$source_id = Request::pint(key: 'loc');
http_response(is_null($source_id), ApiResponse::BADREQUEST, sprintf('location(loc) query parameter is required or invalid: %s', $source_id ?? 'null'));

$mid = Request::pint(key: 'mid');
$comp = Request::pint(key: 'comp');
$rank = Request::pint(key: 'rank');
$faction = Request::pstring(key: 'faction');
$syndicate = Request::pstring(key: 'syndicate');
$mission = Request::mission();


if (!is_null($mid)) {
    DB::mission_remove(universe: $uni, id: $mid);
} 

// If we don't have these two pieces of info ABORT!!!
http_response(is_null($comp) || is_null($rank), ApiResponse::BADREQUEST, sprintf('comp(%s) and rank(%s) query parameter are required or invalid', ($comp ?? 'null'), ($rank ?? 'null')));

$cstart = 0;
if ($comp >= 2) {
    $cstart = $comp - 2;
}
$cend = $comp + 2;

debug('Comp Range = ' . $cstart . ' AND ' . $cend);

// Delete all Non EPS or TSS neutral Missions with the same Comp Level or Lower previously seen at that location
debug('Deleting all Non EPS/TSS neutral missions');
$db->execute(sprintf('DELETE FROM %s_Test_Missions WHERE comp BETWEEN ? AND ? AND faction IS NULL AND source_id = ?', $uni), [
    'iii', $cstart, $cend, $source_id
]);

// Check if our Pilot is a Faction Member
if ($faction) {
    $rstart = 0;
    if ($rank >= 2) {
        $rstart = $rank - 2;
    }
    $rend = $rank + 2;

    debug('Rank Range = ' . $rstart . ' AND ' . $rend);

    // Member of a Faction
    debug('Deleting Faction Missions');
    $db->execute(sprintf('DELETE FROM %s_Test_Missions WHERE rank BETWEEN ? AND ? AND faction = ? AND source_id = ?', $uni), [
        'iisi', $rstart, $rend, $faction, $source_id
    ]);
}

if ($syndicate) {
    // Member of a Syndicate
    debug('Deleting Syndicate Missions');
    $db->execute(sprintf('DELETE FROM %s_Test_Missions WHERE comp BETWEEN ? AND ? AND faction = ? AND source_id = ?', $uni), [
        'iisi', $cstart, $cend, $syndicate, $source_id
    ]);
}

if (count($mission) <= 1) { // because the first row is always empty we need at least 2 rows so, use <= 1
    debug('No Available Mission Data');
    exit;
}

debug('Mission Data Exists');

for ($i = 1; $i < count($mission); $i++) {
    $m = explode(',', (string) $mission[$i]);

    debug($m);

    // Check if Mission Still Exists
    $db->execute(sprintf('SELECT * FROM %s_Test_Missions WHERE id = ?', $uni), [
        'i', $m[0]
    ]);

    // mission faction
    $m_faction = vnull($m[1] ?? null);
    debug('Mission faction = ' . $m_faction);

    if (1 === $db->numRows()) {
        debug('We have Existing Mission Data');
        if (is_null($m_faction)) {
            debug('Faction or Syndicate Mission Mission');
            if ((str_contains((string) $m_faction, 'uni')) || (str_contains((string) $m_faction, 'emp')) || (str_contains((string) $m_faction, 'fed'))) {
                debug('Updating Faction Mission');
                if ($rank - 2 <= $q->rank && $q->rank <= $rank + 2) {
                    $db->execute(sprintf('UPDATE %s_Test_Missions SET rank = ? WHERE id = ?', $uni), [
                        'ii', $rank, $m[0]
                    ]);
                }
            } else {
                debug('Updating Syndicate Mission');
                if ($comp - 2 <= $q->comp && $q->comp <= $comp + 2) {
                    $db->execute(sprintf('UPDATE %s_Test_Missions SET comp = ? WHERE id = ?', $uni), [
                        'ii', $comp, $m[0]
                    ]);
                }
            }
        } else {
            // Are these neutral missions?
            debug('Updating Neutral Mission');
            if ($comp - 2 <= $q->comp && $q->comp <= $comp + 2) {
                $db->execute(sprintf('UPDATE %s_Test_Missions SET comp = ? WHERE id = ?', $uni), [
                    'ii', $comp, $m[0]
                ]);
            }
        }
    } else {
        // Get Sector
        $s = DB::sector(id: $source_id);
        http_response(is_null($s), ApiResponse::BADREQUEST, sprintf('sector not found for loc: %s', $source_id)); // exit if not found in DB

        // Get Cluster Information
        $c = DB::cluster(id: $s->c_id);
        http_response(is_null($c), ApiResponse::BADREQUEST, sprintf('cluster not found for sector: %s(%s)', $source_id, $s->c_id)); // exit if not found in DB

        // Prepare Data

        // Get Building Name,X, and Y
        $x = Coordinates::getX($source_id, $s->s_id, $s->rows);
        $y = Coordinates::getY($source_id, $s->s_id, $s->rows, $x);
        $b = DB::building(id: $source_id, universe: $uni);
        http_response(is_null($b), ApiResponse::BADREQUEST, sprintf('building not found for loc: %s', $source_id)); // exit if not found in DB

        // mission tupe
        $m_type = null;
        if (!is_null($m[2] ?? null)) {
            if (strpos($m[2], "LONG-TERM")) {
                $m[2] = substr($m[2], 0, strpos($m[2], "LONG-TERM"));
            }
            if (strpos($m[2], "(")) {
                $m[2] = substr($m[2], 0, strpos($m[2], "("));
            }

            $m_type = vnull($m[2]);
        }
        debug('Mission type = ' . $m_type);

        // mission image
        $m_type_img = null;
        if (!is_null($m[3] ?? null)) {
            debug('STRPOS -->' . strpos($m[3], 'packages'));
            if (str_contains($m[3], 'packages') || str_contains($m[3], 'smuggle') || str_contains($m[3], 'vip') || str_contains($m[3], 'scout') || str_contains($m[3], 'explosives') || str_contains($m[3], 'espionage')) {
                if (str_contains($m[3], '/')) {
                    $m[3] = substr($m[3], strpos($m[3], '/') + 1);
                }
            }

            $m_type_img = vnull($m[3]);
        }
        debug('Image = ' . $m_type_img);

        // mission amount or hack
        $m_amount = null;
        $m_hack = null;
        $v_ammount = vnull($m[4] ?? null);
        if (!is_null($m[4] ?? null)) {
            if (is_numeric($m[4])) {
                $m_amount = $m[4];
            } else {
                $m_hack = $m[4];
            }
        }
        debug('Mission amount = ' . $m_amount);
        debug('Mission hack = ' . $m_hack);

        // mission location
        $m_loc = vnull($m[5] ?? null);
        debug('Mission Target Loc ' . $m_loc);

        // mission sector and cluster
        $m_sector = vnull($m[6] ?? null);
        $m_cluster = null;
        if (!is_null($m_sector)) {
            $tc = DB::cluster(sector: $m[6]);
            http_response(is_null($tc), ApiResponse::BADREQUEST, sprintf('cluster not found for sector: %s', $m_sector)); // exit if not found in DB
            $m_cluster = $tc->code;
        }
        debug('Mission Cluster ' . $m_cluster);

        // mission coordinates
        $m_x = vnull($m[7] ?? null);
        debug('Mission X ' . $m_x);
        $m_y = vnull($m[8] ?? null);
        debug('Mission Y ' . $m_y);

        // mission time
        $m_time = vnull($m[9] ?? null);
        debug('Mission Time ' . $m_time);

        // mission credits
        $m_credits = vnull($m[10] ?? null);
        debug('Mission Credits ' . $m_credits);

        // mission war points
        $m_war = vnull($m[11] ?? null);
        debug('Mission War Points ' . $m_war);


        debug('Inserting New Mission');  //Why would we do an insert and then multiple updates?  NOTED
        $sql = "INSERT INTO %s_Test_Missions (
                    `id`, `source_id`, `sector`, `cluster`, `loc`, `x`, `y`, `comp`, `rank`, `faction`, `type`, `type_img`, `amount`, `hack`, 
                    `t_loc`, `t_sector`, `t_cluster`, `t_x`, `t_y`, `time`, `credits`, `war`, `spotted`
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, UTC_TIMESTAMP())
        ";
        $params = [
            'iisssiiiisssissssiisis',
            $m[0], $source_id, $s->name, 
            $c->code, $b->name, 
            $x, $y, $comp, $rank, $m_faction, $m_type, $m_type_img, $m_amount, $m_hack,
            $m_loc, $m_sector, $m_cluster, $m_x, $m_y, $m_time, $m_credits, $m_war
        ];

        debug(sprintf($sql, $uni), $params);

        $db->execute(sprintf($sql, $uni), $params);
        
        if ($m_type === "Assassination" && is_null($v_ammount)) {
            debug('We have Coords for a NPC lets add them to the Map');
            DB::npc_add(universe: $uni, image: $m_type_img, id: null, sector: $m_sector, x: (int)$m_x, y: (int)$m_y);
        }
    }

    debug('Updating Dates');
    $db->execute(sprintf('UPDATE %s_Test_Missions SET updated = UTC_TIMESTAMP() WHERE id = ?', $uni), [
        'i', $m[0]
    ]);
}

// Clean up any Errors
$db->execute(sprintf('DELETE FROM %s_Test_Missions WHERE spotted IS NULL', $uni));