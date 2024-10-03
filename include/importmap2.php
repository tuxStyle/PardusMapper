<?php
declare(strict_types=1);

use Pardusmapper\Core\Settings;
use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;
use Pardusmapper\CORS;
use Pardusmapper\DB;
use Pardusmapper\Request;
use Pardusmapper\NPC;

require_once('../app/settings.php');

CORS::pardus();

$cloaked = NPC::cloaked();
$single = NPC::single();
$hack = NPC::hack();
$nonblocking = NPC::nonblocking();
$mobile = NPC::mobile();

$mapdata = Request::mapdata();
http_response(is_null($mapdata), ApiResponse::BADREQUEST, sprintf('mapdata query parameter is required or invalid: %s', $mapdata ?? 'null'));

if (str_contains((string) $mapdata, "sb_")) {
    $site = Settings::$BASE_URL . '/include/importstarbasemap.php?';
    $site .= $_SERVER['QUERY_STRING'];
    header("Location: $site");
    exit();
}

if ($debug) {xp($_REQUEST);}

$time_pre = microtime(true);
$sqlcount = 0;

$db = MySqlDB::instance();  // Create an instance of the Database class

// Set Univers Variable and Session Name
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));


// Get Version
$minVersion = 6.5;
$version = Request::version();
http_response($version < $minVersion, ApiResponse::BADREQUEST, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));

$loc = Request::loc(key: 'id');
http_response(is_null($loc), ApiResponse::BADREQUEST, sprintf('location(loc) query parameter is required or invalid: %s', $loc ?? 'null'));

$sector = Request::sector();
if (is_null($sector)) {
    $sector = Request::sector(key: 's');
}
if ($debug) echo 'Sector = ' . $sector . '<br>';
http_response(is_null($sector), ApiResponse::BADREQUEST, 'sector/s query parameter is required');

// Set Pilot Info
$ip = $_SERVER['REMOTE_ADDR'];

$uid = Request::uid(default: 0);
if ($debug) echo 'User ID = ' . $uid .  '<br>';

$user = Request::user(default: "Unknown");
if ($debug) echo 'User Name = ' . $user .  '<br>';

/****************************************************************************************************
 * 
 * this is all related to (Orion|Artemis|Pegasus)_Hack which doesn't seem to exist anymore
 * 
 ****************************************************************************************************
*/
// $coords = "Unknown";
// $px = "Unknown";
// $py = "Unknown";
// $ps = $sector;
// if (isset($_REQUEST['x'])) {
//     $px = $db->real_escape_string($_REQUEST['x']);
// }
// if ($debug) echo 'pilot x location = ' . $px .  '<br>';

// if (isset($_REQUEST['y'])) {
//     $py = $db->real_escape_string($_REQUEST['y']);$
// }
// if ($debug) echo 'pilot y location = ' . $py .  '<br>';

// if ($debug) echo 'pilot s location = ' . $ps .  '<br>';

//$db->query("INSERT INTO ${uni}_Hack (`username`,`user_id`,`location`,`ip`,`date`) VALUES ('$user','$uid','" . $ps . "[" . $px . "," . $py . "]','$ip',UTC_TIMESTAMP())");
// End Pilot Data

//$db->query("INSERT INTO connection_log (`universe`,`username`,`user_id`,`querycount`,`duration`, `date`) VALUES ('Test','Test2','5','5','1.1',UTC_TIMESTAMP())");
/****************************************************************************************************/

++$sqlcount; // Counting SQL iterations per connection

$maparray = explode('~', (string) $mapdata);
$dataString = implode(",", $maparray);
if ($debug) { xp($maparray); echo '<br>';}

//$last = $db->query("SELECT payload FROM connection_log where username = '$user' order by id desc limit 1");
//$last1 = implode('-',$last);
//if ($debug) echo 'last payload' . $last1 . 'wtf<br>';
//if (!($last == $dataString)) {
//if ($debug) echo 111;

$static = DB::static_locations();
++$sqlcount;

// // Perform the SELECT query
// $db->execute("SELECT * FROM Pardus_Static_Locations");
// // Counting SQL iterations per connection
// ++$sqlcount;

// // Check if the query was successful
// http_response($db->numRows() < 1, ApiResponse::BADREQUEST, 'Missing static locations');

// // Initialize an array to hold the results
// $static = [];

// // Fetch each row as an object
// while ($c = $db->fetchObject()) {
//     $static[] = $c->id;
// }

// // Free the result set
// $db->free();

for ($i = 1; $i < count($maparray); $i++) { //This loop addresses only the current tile

    $temp = explode(',', $maparray[$i]);
    if ($debug) echo xp($temp);

    $id = 'NaN' === $temp[0] ? null : (int)$temp[0];
    if ($id == $loc) { 
        // this scenario will never happen as the foreground in map view can't be an opponent, it's always your ship.
        // Should look to "other ships" screen NOTED
        if ($debug) echo strpos($temp[1], "ponents") != false . ' Evaluation Check';
        http_response(str_contains($temp[1], "ponents") && !(in_array($temp[1], $nonblocking)), ApiResponse::BADREQUEST, '(0) Fatal Error');

        if ($debug) echo (strpos($temp[1], "ponents") . ' vs !strpos($temp[1],"ponents") ');
        if ($debug) echo ((!strpos($temp[1], "ponents") != false) . ' !strpos($temp[1],"ponents") != false');
        if ($debug) echo ((!strpos($temp[1], "ponents") != false) . ' !strpos($temp[1],"ponents") !== false');
        if ($debug) echo ((!strpos($temp[1], "ponents") != false) . ' strpos($temp[1],"ponents") != false');
        if ($debug) echo ((!strpos($temp[1], "ponents") != false) . ' strpos($temp[1],"ponents") == false');
        if ($debug) echo ('WTRF! If 1=' . (!strpos($temp[1], "ponents") != false) . ' then we should be removing any NPCs<br>');

        if (!strpos($temp[1], "ponents") != false) {
            // No NPC or Ships on current tile????
            //if ($debug) echo 'WTF!';
            //if ($debug) echo strpos($temp[1],"ships");
            if ($debug) echo $temp[1];
            if ($debug) echo '??NPC has been killed<br>';
            if ($debug) echo 'Blow Away 6<br>';
            $db->removeNPC($uni, $id); //removed to debug 9.16.24
            ++$sqlcount; // Counting SQL iterations per connection
            //die("0,Fatal Error");
        }
    }
}

//if ($debug) echo count($maparray);
for ($i = 1; $i < sizeof($maparray); $i++) { //Not the tiles the ship is on ideally...
    global $sqlcount;
    $temp = explode(',', $maparray[$i]);
    if ($debug) xd($temp);
    $id = 'NaN' === $temp[0] ? null : (int)$temp[0];
    //if ($debug) echo $id;
    //$db->query("INSERT INTO connection_log (`universe`,`username`,`user_id`,`querycount`,`duration`, `date`) VALUES ('Test','$i','5','5','1.1',UTC_TIMESTAMP())");
    if (in_array($id, $static)) {
        continue;
    }
    // Check to see if we got good data
    if (!strpos($temp[1], "nodata.png") && !is_null($id)) {
        if ($debug) echo $id . ' Does Not Contain "nodata.png"<br>';
        // Check to see if we got Building Info
        $r_bg = 0;
        if (str_contains($temp[1], "foregrounds")) {
            if ($debug) echo $id . ' Contains "Foreground" Info<br>';
            $r_fg = 1;
            $r_npc = 0;
            if ((str_contains($temp[1], "wormhole")) || (str_contains($temp[1], "xhole")) || (str_contains($temp[1], "yhole"))) {
                if (sizeof($temp) != 3) {
                    if ($debug) echo $id, ' Contains "Critter" Info not "Foreground" Info<br>';
                    $r_fg = 0;
                    $r_npc = 1;
                }
            }
            // Check to see if we got Critter info
        } elseif (str_contains($temp[1], "ponents")) {
            if ($debug) echo $id . ' Contains "Critter" Info<br>';
            $r_fg = 0;
            $r_npc = 1;
            if (isset($temp[2]) && $temp[2] !== false) {
                $r_fg = 0;
                $dbClass->removeBuilding($uni, $id, 0); // can't have a building if we can see the NPC from the map view
                ++$sqlcount; // Counting SQL iterations per connection
            }
            // Must be a Ship or something I don't want
        } elseif (str_contains($temp[1], "xmas-star")) {
            if ($debug) echo $id . ' Contains Xmas Info<br>';
            $r_fg = 1;
            $r_npc = 0;
        } else {
            if ($debug) echo $id . ' Contains "Background" Info<br>';
            $r_fg = 0;
            $r_npc = 0;
            $r_bg = 1;
            //$db->removeBuilding($uni,$id,0);  // can't have a building if we can see the background only from the map view
                                                // This might be a bit strong, we don't need to delete non-existant data NOTED
            //++$sqlcount; // Counting SQL iterations per connection
        }

        // Check to see if we have Info for the current tile
        // Insert new data if there is not current info
        // Do Nothing if there is current info
        // This should not be the case with a complete map as every ID should be in the system, consider removing? REMOVED 4.2.20
        // Perform the initial query
        if ($debug) xp(sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_Maps WHERE id = ?', $uni), [
            'i', $id
        ]);
        $result = $db->execute(sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_Maps WHERE id = ?', $uni), [
            'i', $id
        ]);

        // Check for query errors
        http_response(!$result, ApiResponse::BADREQUEST, sprintf('(1) Query failed: %s', $db->getDb()->error));

        // Counting SQL iterations per connection
        ++$sqlcount;

        // Fetch the first row
        $r = $db->fetchObject();

        // Check if any row was returned
        if (!$r) {
            // There is no existing information for the current tile
            if ($debug) {
                echo $id . ' New Information Inserting into DB<br>';
            }

            // Call method to add new map
            $db->addMap($uni, $temp[$r_bg], $id, 0);

            // Counting SQL iterations per connection
            ++$sqlcount;

            // Perform the query again after adding new map
            $result = $db->execute(sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_Maps WHERE id = ?', $uni), [
                'i', $id
            ]);
    
            // Check for query errors
            http_response(!$result, ApiResponse::BADREQUEST, sprintf('(2) Query failed: %s', $db->getDb()->error));

            // Counting SQL iterations per connection
            ++$sqlcount;

            // Fetch the updated row
            $r = $db->fetchObject();
        }

        // Free the result set
        $db->free();


        if ($debug) {xd(__FILE__, __LINE__, $r); echo '<br>';}

        // // Why would we not have sector and cluster?  This should be disabled?
        //if (is_null($r->cluster) || is_null($r->sector)) {
        //	if ($debug) echo $id . ' Sector and/or Cluster is Null<br>';
        //	$s = $db->getSector($id,"");
        //	$db->query('UPDATE ' . $uni . '_Maps SET sector = \'' . $s->name . '\' WHERE id = ' . $id);
        //	$c = $db->getCluster($s->c_id,"");
        //	$db->query('UPDATE ' . $uni . '_Maps SET cluster = \'' . $c->name . '\' WHERE id = ' . $id);
        //} // We also have all the X/Y coords...again we should remove this?
        //if ($r->x == 0 || $r->y == 0) {
        //	if ($debug) echo $id . ' X and/or Y is Null<br>';
        //	if (!$s) { $s = $db->getSector($id,""); }
        //	$x = $db->getX($id,$s->s_id,$s->rows);
        //	$db->query('UPDATE ' . $uni . '_Maps SET x = ' . $x . ' WHERE id = ' . $id);
        //	$y = $db->getY($id,$s->s_id,$s->rows,$x);
        //	$db->query('UPDATE ' . $uni . '_Maps SET y = ' . $y . ' WHERE id = ' . $id);
        //} 
        // If we are seeing the Nav Screen at the current location then there is NPC
        // Check to see if we have Wormhole destination info

        //Removing WH addition logic...just doesn't make sense while the Universes are stable - 3.17.2021 JT
        /*
			if ($debug) echo $id . ' Size = ' . sizeof($temp) . '<br>';
			if (sizeof($temp) == 3 && !(strpos($temp[1],"ponents") !== false )) {  // this is where wormholes are updated as long as they aren't an opponent (no adding)...keeping for now NOTED
				if ($debug) echo $temp[1].'---'.!(strpos($temp[1],"ponents") !== false) . 'We think this is a Wormhole';
				if ($temp[2] != 'unknown') {
					$db->query('UPDATE ' . $uni . '_Maps SET `wormhole` = \'' . $temp[2] . '\' WHERE id = ' . $id);
					++$sqlcount; // Counting SQL iterations per connection
				}
				$db->query ('UPDATE ' . $uni . '_Maps SET `fg` = \'' . $temp[$r_fg] . '\' WHERE id = ' . $id);
				++$sqlcount; // Counting SQL iterations per connection
				$db->query ('UPDATE ' . $uni . '_Maps SET `npc` = NULL WHERE id = ' . $id);
				++$sqlcount; // Counting SQL iterations per connection
				$r->wormhole = $temp[2];
				continue;
			} 
			if ($r->wormhole) { continue; }
			*/
        if ($r_fg != 0) {
            // Check to see if we have Foreground information for the current tile
            // If we do not then we need to double check for existing info and remove it.
            if ($debug) echo $id . ' Building information exists for current location<br>';
            // Check to See if the DB is NULL
            if (is_null($r->fg)) {
                // DB is NULL Just Add new Info
                if ($debug) echo 'Adding Building<br>';
                $db->addBuilding($uni, $temp[$r_fg], $id, 0);
                ++$sqlcount; // Counting SQL iterations per connection
            } else if (sizeof($temp) != 3) { // this isn't an NPC record from the non-blocking window
                //Test to See if Map and DB match
                if ($debug) echo 'Testing New FG - ' . str_replace("_tradeoff", "", $temp[$r_fg]) . '<br>';
                if ($debug) echo 'Testing DB FG - ' . str_replace("_tradeoff", "", $r->fg) . '<br>';
                if (str_replace("_tradeoff", "", $temp[$r_fg]) != str_replace("_tradeoff", "", $r->fg)) {
                    if ($debug) echo $id . ' Foreground info Does Not Matches DB<br>';
                    // See if we have a Gem merchant
                    if (str_contains($temp[$r_fg], "gem_merchant")) {
                        // Perform the query
                        $result = $db->execute(sprintf('SELECT * FROM %s_Maps WHERE fg = ? AND cluster = ?', $uni), [
                            'ss', $temp[$r_fg], $r->cluster
                        ]);

                        // Check for query errors
                        http_response(!$result, ApiResponse::BADREQUEST, sprintf('(3) Query failed: %s', $db->getDb()->error));


                        // Counting SQL iterations per connection
                        ++$sqlcount;

                        // Initialize an array to hold the results
                        $gems = [];

                        // Fetch each row as an object
                        while ($g = $db->fetchObject()) {
                            $gems[] = $g;
                        }

                        // Free the result set
                        $db->free();

                        // Process each gem
                        foreach ($gems as $g) {
                            $db->removeBuilding($uni, $g->id, 0);
                            // Counting SQL iterations per connection
                            ++$sqlcount;
                        }
                    }
                    if ($debug) {
                        echo $id . ' Deleting Old Building<br>';
                    }
                    $db->removeBuilding($uni, $id, 0);
                    ++$sqlcount; // Counting SQL iterations per connection
                    if ($debug) {
                        echo $id . ' Inserting New Building<br>';
                    }
                    $db->addBuilding($uni, $temp[$r_fg], $id, 0);
                    ++$sqlcount; // Counting SQL iterations per connection
                } else {
                    if ($debug) echo $id . ' Foreground info Matches DB<br>';
                    $db->updateMapFG($uni, $temp[$r_fg], $id);
                    ++$sqlcount; // Counting SQL iterations per connection
                    if ($temp[$r_fg] != $r->fg) {
                        if ($debug) echo $id . ' Foreground Image Changed<br>';
                        $db->execute(sprintf('UPDATE %s_Buildings set image = ? WHERE id = ?', $uni), [
                            'si', $temp[$r_fg], $id
                        ]);
                        ++$sqlcount; // Counting SQL iterations per connection
                    }
                }
            }
        } else {
            if ($debug) echo $id . ' No Foreground info to worry about<br>';
        }
        if ($debug) echo 'this is result ' . $r_npc . 'Temp 2 check<br>';
        if ($r_npc != 0) { //we have an NPC on the map but no NPC ID to record (non blocking NPC with newer mapper script)
            if (!is_null($r->fg) && (isset($temp[2]) && $temp[2] !== false)) { // we have an NPC on the map but not an NID, thus can't have a building or SB or we wouldn't see the NPC
                if (strpos($r->fg, "starbase")) {
                    $db->removeBuilding($uni, $id, 1);
                    ++$sqlcount; // Counting SQL iterations per connection
                } else {
                    if ($debug) echo 'Blowing Away a building';
                    $db->removeBuilding($uni, $id, 0);
                    ++$sqlcount; // Counting SQL iterations per connection
                }
            }
            if ($debug) echo $id . ' Checking NPC data<br>';
            if (in_array($id, $static)) {
                continue;
            }
            if ($debug) echo $id . ' Not Static NPC<br>';
            if ($debug) xp($temp[$r_npc], $r->npc, $temp[$r_npc] == $r->npc, $r->fg);
            if (is_null($r->npc)) {
                if ($debug) echo $id . ' No NPC Data in DB<br>';
                if (is_null($r->fg) || $temp[2]) { //Add check that we say the FG is not a building but the DB might have something?
                    //if (is_null($r->fg)||$r_fg == 0) { //Added check that we say the FG is not a building but the DB might have something
                    if ($debug) echo $id . ' setting $npc = ' . $temp[$r_npc] . ' and then' . ' Adding New NPC<br>';
                    $npc = $temp[$r_npc];
                    // if (in_array($npc, $hack)) {
                    //     $ip = $_SERVER['REMOTE_ADDR'];
                    //     $db->query("SELECT * FROM {$uni}_Users WHERE `ip` = '$ip'");
                    //     ++$sqlcount; // Counting SQL iterations per connection
                    //     //if ($user = $dbClass->nextObject()) { //removed in debugging 9.16.24
                    //     //not sure what this was for but it won't work in current instance as the DB fields have changed
                    //     //$db->query("INSERT INTO ${uni}_Hack (`id`,`username`,`version`,`image`,`date`) VALUES ($user->id,'$user->username','$version','$npc',$UTC_TIMESTAMP())");
                    //     ++$sqlcount; // Counting SQL iterations per connection
                    //     //}
                    // }
                    if (in_array($temp[$r_npc], $single)) {
                        // NPC is only one per Cluster Find location of previous Instance
                        $db->execute(sprintf("SELECT * FROM %s_Maps WHERE cluster = ? AND npc = ?", $uni), [
                            'ss', $r->cluster, $npc
                        ]);
                        ++$sqlcount; // Counting SQL iterations per connection
                        while ($t = $db->nextObject()) {
                            $to_delete[] = $t->id;
                        }
                        if ($to_delete) {
                            for ($t = 0; $t < count($to_delete); $t++) {
                                // Delete NPC from Data Table
                                if ($debug) echo 'Blow Away 1';
                                $db->removeNPC($uni, $to_delete[$t]);
                                ++$sqlcount; // Counting SQL iterations per connection
                            }
                        }
                    }
                    $nid = isset($temp[2]) ? (int)$temp[2] : null;
                    if ($debug) echo 'Adding NPC with nid' . ($uni . $temp[$r_npc] . $id . $nid) . '<br>';
                    $db->addNPC($uni, $temp[$r_npc], $id, " ", 0, 0, $nid); // Adding nid
                    ++$sqlcount; // Counting SQL iterations per connection
                }
            } elseif ($temp[$r_npc] == $r->npc) { // it's the same NPC the map shows
                $nid = isset($temp[2]) ? (int)$temp[2] : null;
                if ($debug) echo $id . ' with nid = ' . $nid . ' and Uncloaked<br>';
                if (is_null($nid)) {
                    $db->updateMapNPC($uni, $temp[$r_npc], $id, 0, $nid);
                } else {
                    // Handle the case where $temp[2] is not set
                    $db->updateMapNPC($uni, $temp[$r_npc], $id, 0); // Adding nid if known as well
                    if ($debug) echo "Error: \$temp[2] is not set.";
                }
                ++$sqlcount; // Counting SQL iterations per connection
            } else {
                $nid = isset($temp[2]) ? (int)$temp[2] : null;
                if ($debug) echo $id . ' with nid = ' . $nid . ' and Uncloaked<br>';

                if ($debug) echo $id . ' Has a Different NPC so Blow Away 2<br>';
                $db->removeNPC($uni, $id);
                ++$sqlcount; // Counting SQL iterations per connection
                $db->addNPC($uni, $temp[$r_npc], $id, "", 1, $nid); // Adding nid
                ++$sqlcount; // Counting SQL iterations per connection
            }
        } else {
            if ($debug) echo $id . ', - ' . $temp[$r_npc] . ', - ' . $r->npc . ' No NPC info to worry about<br>';
        }
        if ($r_bg != 0) {
            //Check to see if we have Building information for the current tile
            if (!(is_null($r->fg))) {
                // We do have Building Information  //Need to check for Starbase functionality NOTED
                //if ($id == $_REQUEST['id']) { //Not sure what this check is doing as id is our current location which means we can only remove a building we are standing on? NOTED
                if ($debug) echo $id . ' Deleting Foreground info from DB<br>';
                if (strpos($r->fg, "starbase")) {
                    $db->removeBuilding($uni, $id, 1);
                    ++$sqlcount; // Counting SQL iterations per connection
                } else {
                    $db->removeBuilding($uni, $id, 0);
                    ++$sqlcount; // Counting SQL iterations per connection
                }
                //}
            }
            if (!(is_null($r->npc))) { // Check if NPC exists
                if ($debug) {
                    if (($id == $_REQUEST['id']) && (isset($temp[2]) && $temp[2] !== false) && (str_contains($temp[1], "ships")) && !(in_array($r->npc, $nonblocking))) {
                        if ($debug) echo '--result of test for Blow Away 3';
                    }
                }

                if (($id == $_REQUEST['id']) && (isset($temp[2]) && $temp[2] !== false) && (str_contains($temp[1], "ships")) && !(in_array($r->npc, $nonblocking))) {
                    if ($debug) echo 'is this working or what<br>';
                    if ($debug) echo '??NPC has been killed<br>';
                    if ($debug) echo 'Blow Away 3<br>';
                    $db->removeNPC($uni, $id);
                    ++$sqlcount; // Counting SQL iterations per connection
                } else {
                    if ((in_array($r->npc, $cloaked)) && !(in_array($r->npc, $mobile))) {
                        if ($debug) echo 'NPC has cloaked<br>';
                        $nid = isset($temp[2]) ? (int)$temp[2] : null;
                        if (is_null($r->npc_cloaked)) {
                            $db->updateMapNPC($uni, $temp[$r_npc], $id, 1, $nid);
                            ++$sqlcount; // Counting SQL iterations per connection
                        } else {
                            $show = strtotime($r->today) - strtotime($r->npc_updated);
                            if ($show > 432000) {
                                if ($debug) echo 'Blow Away 4<br>';
                                $db->removeNPC($uni, $id);
                                ++$sqlcount; // Counting SQL iterations per connection
                            }
                        }
                    } else {
                        if ($debug) echo '???NPC has been killed<br>';
                        if ($debug) echo $id . '-' . $_REQUEST['id'];
                        if ($debug) echo '???Now Trying to remove the record<br>';
                        if ($debug) echo 'Blow Away 5<br>';
                        $db->removeNPC($uni, $id);
                        ++$sqlcount; // Counting SQL iterations per connection
                    }
                }
            }
        }
    }
}
//}

++$sqlcount;
$time_post = microtime(true);
$exec_time = $time_post - $time_pre;
$db->execute("INSERT INTO connection_log (`universe`,`username`,`user_id`,`querycount`,`duration`, `date`, `payload`) VALUES (?, ?, ?, ?, ?, UTC_TIMESTAMP(), ?)", [
    'ssiidb', $uni, $user, $uid, $sqlcount, $exec_time, $dataString
]);
$db->close();
$db = null;
