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

$db = MySqlDB::instance();

// Test Mission Table

// Set Univers Variable and Session Name
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

// Get Version
$minVersion = 5.8;
$version = Request::version();
http_response($version < $minVersion, ApiResponse::BADREQUEST, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));

// Set Location
$source_id = Request::loc();
http_response(is_null($source_id), ApiResponse::BADREQUEST, sprintf('location(loc) query parameter is required or invalid: %s', $source_id ?? 'null'));
if ($debug) echo 'Source ID = ' . $source_id .  '<br>';

$mid = Request::mid();
if ($debug) echo 'Mission ID = ' . $mid .  '<br>';

$comp = Request::comp();
if ($debug) echo 'Comp = ' . $comp .  '<br>';

$rank = Request::rank();
if ($debug) echo 'Rank = ' . $rank .  '<br>';

$faction = Request::faction();
if ($debug) echo 'faction = ' . $faction .  '<br>';

$syndicate = Request::syndicate();
if ($debug) echo 'syndicate = ' . $syndicate .  '<br>';

$mission = Request::mission();


if (!is_null($mid)) {
    $db->removeMission($uni, $mid);
} 

// If we don't have these two pieces of info ABORT!!!
http_response(is_null($comp) || is_null($rank), ApiResponse::BADREQUEST, sprintf('comp(%s) and rank(%s) query parameter are required or invalid', ($comp ?? 'null'), ($rank ?? 'null')));

$cstart = 0;
if ($comp >= 2) {
    $cstart = $comp - 2;
}
$cend = $comp + 2;

if ($debug) echo 'Comp Range = ' . $cstart . ' AND ' . $cend . '<br>';

// Delete all Non EPS or TSS neutral Missions with the same Comp Level or Lower previously seen at that location
if ($debug) echo 'Deleting all Non EPS/TSS neutral missions<br>';
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

    if ($debug) echo 'Rank Range = ' . $rstart . ' AND ' . $rend . '<br>';

    // Member of a Faction
    if ($debug) echo 'Deleting Faction Missions<br>';
    $db->execute(sprintf('DELETE FROM %s_Test_Missions WHERE rank BETWEEN ? AND ? AND faction = ? AND source_id = ?', $uni), [
        'iisi', $rstart, $rend, $faction, $source_id
    ]);
}

if ($syndicate) {
    // Member of a Syndicate
    if ($debug) echo 'Deleting Syndicate Missions<br>';
    $db->execute(sprintf('DELETE FROM %s_Test_Missions WHERE comp BETWEEN ? AND ? AND faction = ? AND source_id = ?', $uni), [
        'iisi', $cstart, $cend, $syndicate, $source_id
    ]);
}

if (count($mission) <= 1) { // because the first row is always empty we need at least 2 rows so, use <= 1
    if ($debug) echo 'No Available Mission Data<br>';
    exit;
}

if ($debug) echo 'Mission Data Exists<br>';

for ($i = 1; $i < count($mission); $i++) {
    $m = explode(',', $mission[$i]);

    if ($debug) xd($m);
    if ($debug) echo '<br>';

    // Check if Mission Still Exists
    $db->execute(sprintf('SELECT * FROM %s_Test_Missions WHERE id = ?', $uni), [
        'i', $m[0]
    ]);

    // mission faction
    $m_faction = vnull($m[1] ?? null);
    if ($debug) echo 'Mission faction = ' . $m_faction . '<br>';

    if (1 === $db->numRows()) {
        if ($debug) echo 'We have Existing Mission Data<br>';
        if (is_null($m_faction)) {
            if ($debug) echo 'Faction or Syndicate Mission Mission<br>';
            if ((str_contains($m_faction, 'uni')) || (str_contains($m_faction, 'emp')) || (str_contains($m_faction, 'fed'))) {
                if ($debug) echo 'Updating Faction Mission<br>';
                if ($rank - 2 <= $q->rank && $q->rank <= $rank + 2) {
                    $db->execute(sprintf('UPDATE %s_Test_Missions SET rank = ? WHERE id = ?', $uni), [
                        'ii', $rank, $m[0]
                    ]);
                }
            } else {
                if ($debug) echo 'Updating Syndicate Mission<br>';
                if ($comp - 2 <= $q->comp && $q->comp <= $comp + 2) {
                    $db->execute(sprintf('UPDATE %s_Test_Missions SET comp = ? WHERE id = ?', $uni), [
                        'ii', $comp, $m[0]
                    ]);
                }
            }
        } else {
            // Are these neutral missions?
            if ($debug) echo 'Updating Neutral Mission<br>';
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
        $b = DB::building($source_id, $uni);
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
        if ($debug) echo 'Mission type = ' . $m_type . '<br>';

        // mission image
        $m_type_img = null;
        if (!is_null($m[3] ?? null)) {
            if ($debug) echo 'STRPOS -->' . strpos($m[3], 'packages') . '<br>';
            if (str_contains($m[3], 'packages') || str_contains($m[3], 'smuggle') || str_contains($m[3], 'vip') || str_contains($m[3], 'scout') || str_contains($m[3], 'explosives') || str_contains($m[3], 'espionage')) {
                if (str_contains($m[3], '/')) {
                    $m[3] = substr($m[3], strpos($m[3], '/') + 1);
                }
            }

            $m_type_img = vnull($m[3]);
        }
        if ($debug) echo 'Image = ' . $m_type_img . '<br>';

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
        if ($debug) echo 'Mission amount = ' . $m_amount . '<br>';
        if ($debug) echo 'Mission hack = ' . $m_hack . '<br>';

        // mission location
        $m_loc = vnull($m[5] ?? null);
        if ($debug) echo 'Mission Target Loc ' . $m_loc . '<br>';

        // mission sector and cluster
        $m_sector = vnull($m[6] ?? null);
        $m_cluster = null;
        if (!is_null($m_sector)) {
            $tc = DB::cluster(sector: $m[6]);
            http_response(is_null($tc), ApiResponse::BADREQUEST, sprintf('cluster not found for sector: %s', $m_sector)); // exit if not found in DB
            $m_cluster = $tc->code;
        }
        if ($debug) echo 'Mission Cluster ' . $m_cluster . '<br>';

        // mission coordinates
        $m_x = vnull($m[7] ?? null);
        if ($debug) echo 'Mission X ' . $m_x . '<br>';
        $m_y = vnull($m[8] ?? null);
        if ($debug) echo 'Mission Y ' . $m_y . '<br>';

        // mission time
        $m_time = vnull($m[9] ?? null);
        if ($debug) echo 'Mission Time ' . $m_time . '<br>';

        // mission credits
        $m_credits = vnull($m[10] ?? null);
        if ($debug) echo 'Mission Credits ' . $m_credits . '<br>';

        // mission war points
        $m_war = vnull($m[11] ?? null);
        if ($debug) echo 'Mission War Points ' . $m_war . '<br>';


        if ($debug) echo 'Inserting New Mission<br>';  //Why would we do an insert and then multiple updates?  NOTED
        $sql = "INSERT INTO %s_Test_Missions (
                    id, source_id, sector, cluster, loc, x, y, comp, rank, faction, `type`, type_img, amount, hack, 
                    t_loc, t_sector, t_cluster, t_x, t_y, `time`, credits, war, spotted
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, UTC_TIMESTAMP())
        ";
        $params = [
            'iisssiiiisssissssiisis',
            $m[0], $source_id, $s->name, 
            $c->code, $b->name, 
            $x, $y, $comp, $rank, $m_faction, $m_type, $m_type_img, $m_amount, $m_hack,
            $m_loc, $m_sector, $m_cluster, $m_x, $m_y, $m_time, $m_credits, $m_war
        ];

        if ($debug) {
            xp(sprintf($sql, $uni), $params);
        }

        $db->execute(sprintf($sql, $uni), $params);
        
        if ($m_type === "Assassination" && is_null($v_ammount)) {
            if ($debug) {
                echo 'We have Coords for a NPC lets add them to the Map<br>';
            }
            $db->addNPC($uni, $m_type_img, 0, $m_sector, (int)$m_x, (int)$m_y);
        }
    }

    if ($debug) echo 'Updating Dates<br>';
    $db->execute(sprintf('UPDATE %s_Test_Missions SET updated = UTC_TIMESTAMP() WHERE id = ?', $uni), [
        'i', $m[0]
    ]);
}

// Clean up any Errors
$db->execute(sprintf('DELETE FROM %s_Test_Missions WHERE spotted IS NULL', $uni));