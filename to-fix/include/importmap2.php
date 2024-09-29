<?php
declare(strict_types=1);

if (isset($_SERVER['HTTP_ORIGIN']) && preg_match('/^https?:\/\/(orion|artemis|pardus)?\.pardus\.at$/i', (string) $_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']); // Dynamically allow the origin
} else {
    die('0,Information Not coming from Pardus');
}

require_once('../app/settings.php');

use Pardusmapper\Core\Settings;
use Pardusmapper\Core\MySqlDB;

if (str_contains((string) $_REQUEST['mapdata'], "sb_")) {
    $site = Settings::$BASE_URL . '/include/importstarbasemap.php?';
    $site .= $_SERVER['QUERY_STRING'];
    header("Location: $site");
}

$time_pre = microtime(true);
$sqlcount = 0;

$dbClass = new MySqlDB();  // Create an instance of the Database class
$db = $dbClass->getDb();    // Get the mysqli connection object

if (!isset($_REQUEST['debug'])) {
    $debug = false;
}

// Set Univers Variable and Session Name
if (!isset($_REQUEST['uni'])) {
    exit;
}

$uni = $db->real_escape_string($_REQUEST['uni']);

if ($debug) echo '1Universe = ' . $uni . '<br>';

// Get Version
$version = 0;
if (isset($_REQUEST['version'])) {
    $version = $db->real_escape_string($_REQUEST['version']);
}

if ($debug) echo 'Version = ' . $version . '<br>';
if ($version < 6.5) {
    exit;
}

if (!isset($_REQUEST['mapdata'])) {
    exit;
}

$loc = (int)$db->real_escape_string($_REQUEST['id']);
if ($debug) echo 'Location = ' . $loc . '<br>';

if (isset($_REQUEST['s'])) {
    $sector = $db->real_escape_string($_REQUEST['s']);
} else {
    $sector = $db->real_escape_string($_REQUEST['sector']);
}
if ($debug) echo 'Sector = ' . $sector . '<br>';

// Set Pilot Info

$ip = $_SERVER['REMOTE_ADDR'];

$uid = 0;
if (isset($_REQUEST['uid'])) {
    $uid = $db->real_escape_string($_REQUEST['uid']);
}
if ($debug) echo 'User ID = ' . $uid .  '<br>';

$user = "Unknown";
if (isset($_REQUEST['user'])) {
    $user = $db->real_escape_string($_REQUEST['user']);
}
if ($debug) echo 'User Name = ' . $user .  '<br>';

$coords = "Unknown";
$px = "Unknown";
if (isset($_REQUEST['x'])) {
    $px = $db->real_escape_string($_REQUEST['x']);
}
if ($debug) echo 'pilot x location = ' . $px .  '<br>';

$py = "Unknown";
if (isset($_REQUEST['y'])) {
    $py = $db->real_escape_string($_REQUEST['y']);
}
if ($debug) echo 'pilot y location = ' . $py .  '<br>';

$ps = $sector;
if ($debug) echo 'pilot s location = ' . $ps .  '<br>';

//$db->query("INSERT INTO ${uni}_Hack (`username`,`user_id`,`location`,`ip`,`date`) VALUES ('$user','$uid','" . $ps . "[" . $px . "," . $py . "]','$ip',UTC_TIMESTAMP())");
// End Pilot Data

//$db->query("INSERT INTO connection_log (`universe`,`username`,`user_id`,`querycount`,`duration`, `date`) VALUES ('Test','Test2','5','5','1.1',UTC_TIMESTAMP())");

++$sqlcount; // Counting SQL iterations per connection

$mapdata = $db->real_escape_string($_REQUEST['mapdata']);

$maparray = explode('~', (string) $mapdata);
$dataString = implode(",", $maparray);
//$last = $db->query("SELECT payload FROM connection_log where username = '$user' order by id desc limit 1");
//$last1 = implode('-',$last);
//if ($debug) echo 'last payload' . $last1 . 'wtf<br>';
//if (!($last == $dataString)) {
if ($debug) print_r($maparray);
if ($debug) echo '<br>';

$cloaked[] = 'opponents/blood_amoeba.png';
$cloaked[] = 'opponents/ceylacennia.png';
$cloaked[] = 'opponents/cyborg_manta.png';
$cloaked[] = 'opponents/manifestation_developed.png';
$cloaked[] = 'opponents/dreadscorps.png';
$cloaked[] = 'opponents/drosera.png';
$cloaked[] = 'opponents/energy_minnow.png';
$cloaked[] = 'opponents/energy_sparker.png';
$cloaked[] = 'opponents/smuggler_escorted.png';
$cloaked[] = 'opponents/pirate_experienced.png';
$cloaked[] = 'opponents/pirate_famous.png';
$cloaked[] = 'opponents/frost_crystal.png';
$cloaked[] = 'opponents/gorefanglings.png';
$cloaked[] = 'opponents/gorefangling.png';
$cloaked[] = 'opponents/gorefang.png';
$cloaked[] = 'opponents/hidden_drug_stash.png';
$cloaked[] = 'opponents/pirate_inexperienced.png';
$cloaked[] = 'opponents/infected_creature.png';
$cloaked[] = 'opponents/smuggler_lone.png';
$cloaked[] = 'opponents/lucidi_squad.png';
$cloaked[] = 'opponents/nebula_mole.png';
$cloaked[] = 'opponents/nebula_serpent.png';
$cloaked[] = 'opponents/oblivion_vortex.png';
$cloaked[] = 'opponents/manifestation_ripe';
$cloaked[] = 'opponents/sarracenia.png';
$cloaked[] = 'opponents/slave_trader.png';
$cloaked[] = 'opponents/manifestation_verdant.png';
$cloaked[] = 'opponents/locust_hive.png';
$cloaked[] = 'opponents/vyrex_hatcher.png';
$cloaked[] = 'opponents/vyrex_assassin.png';
$cloaked[] = 'opponents/vyrex_stinger.png';
$cloaked[] = 'opponents/vyrex_mutant_mauler.png';
$cloaked[] = 'opponents/vyrex_larva.png';
$cloaked[] = 'opponents/pirate_captain.png';
$cloaked[] = 'opponents/starclaw.png';

sort($cloaked);

$single[] = 'opponents/shadow.png';
$single[] = 'opponents/feral_serpent.png';

sort($single);

$hack[] = 'opponents/shadow.png';
$hack[] = 'opponents/pirate_experienced.png';
$hack[] = 'opponents/energybees.png';

sort($hack);

$nonblocking[] = 'opponents/slave_trader.png';
$nonblocking[] = 'opponents/smuggler_lone.png';
$nonblocking[] = 'opponents/smuggler_escorted.png';
//$nonblocking[] = 'foreground/wormhole.png';
$nonblocking[] = 'opponents/gorefanglings.png';
$nonblocking[] = 'opponents/gorefang.png';
$nonblocking[] = 'opponents/nebula_mole.png';
$nonblocking[] = 'opponents/hidden_drug_stash.png';
$nonblocking[] = 'opponents/space_clam.png';
$nonblocking[] = 'opponents/preywinder.png';
$nonblocking[] = 'opponents/glowprawn.png';
$nonblocking[] = 'opponents/starclaw.png';
$nonblocking[] = 'opponents/eulerian.png';
$nonblocking[] = 'opponents/vyrex_hatcher.png';
$nonblocking[] = 'opponents/vyrex_assassin.png';
$nonblocking[] = 'opponents/vyrex_stinger.png';
$nonblocking[] = 'opponents/vyrex_mutant_mauler.png';
$nonblocking[] = 'opponents/vyrex_larva.png';

sort($nonblocking);

// These NPC move around but may have limited count, need to not show cloaked location if another is spotted within the same range(new query needed to find ranged matches) NOTED
$mobile[] = 'opponents/space_dragon_queen.png';
$mobile[] = 'opponents/cyborg_manta.png';
$mobile[] = 'opponents/lucidi_squad.png';
$mobile[] = 'opponents/pirate_famous.png';
$mobile[] = 'opponents/pirate_captain.png';
$mobile[] = 'opponents/preywinder.png';
$mobile[] = 'opponents/vyrex_mutant_mauler.png';

sort($mobile);
//if ($debug) echo 111;
// Perform the SELECT query
$result = $db->query("SELECT * FROM Pardus_Static_Locations");

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $db->error);
}

// Counting SQL iterations per connection
++$sqlcount;

// Initialize an array to hold the results
$static = [];

// Fetch each row as an object
while ($c = $result->fetch_object()) {
    $static[] = $c->id;
}

// Free the result set
$result->free();

for ($i = 1; $i < sizeof($maparray); $i++) { //This loop addresses only the current tile

    $temp = explode(',', $maparray[$i]);
    $id = 'NaN' === $temp[0] ? null : (int)$temp[0];
    if ($debug) echo print_r($temp);
    if ($id == $loc) { // this scenario will never happen as the foreground in map view can't be an opponent, it's always your ship.  Should look to "other ships" screen NOTED
        if ($debug) echo strpos($temp[1], "ponents") != false . ' Evaluation Check';
        if (str_contains($temp[1], "ponents") && !(in_array($temp[1], $nonblocking))) {
            die("0,Fatal Error");
        }
        if ($debug) echo (strpos($temp[1], "ponents") . ' vs !strpos($temp[1],"ponents") ');
        if ($debug) echo ((!strpos($temp[1], "ponents") != false) . ' !strpos($temp[1],"ponents") != false');
        if ($debug) echo ((!strpos($temp[1], "ponents") != false) . ' !strpos($temp[1],"ponents") !== false');
        if ($debug) echo ((!strpos($temp[1], "ponents") != false) . ' strpos($temp[1],"ponents") != false');
        if ($debug) echo ((!strpos($temp[1], "ponents") != false) . ' strpos($temp[1],"ponents") == false');
        if ($debug) echo ('WTRF! If 1=' . (!strpos($temp[1], "ponents") != false) . ' then we should be removing any NPCs<br>');
        if (!strpos($temp[1], "ponents") != false) { // No NPC or Ships on current tile????
            //if ($debug) echo 'WTF!';
            //if ($debug) echo strpos($temp[1],"ships");
            if ($debug) echo $temp[1];
            if ($debug) echo '??NPC has been killed<br>';
            if ($debug) echo 'Blow Away 6<br>';
            $dbClass->removeNPC($uni, $id); //removed to debug 9.16.24
            ++$sqlcount; // Counting SQL iterations per connection
            //die("0,Fatal Error");
        }
    }
}
//if ($debug) echo sizeof($maparray);
for ($i = 1; $i < sizeof($maparray); $i++) { //Not the tiles the ship is on ideally...
    global $sqlcount;
    $temp = explode(',', $maparray[$i]);
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
            //$db->removeBuilding($uni,$id,0); // can't have a building if we can see the background only from the map view // This might be a bit strong, we don't need to delete non-existant data NOTED
            //++$sqlcount; // Counting SQL iterations per connection
        }

        // Check to see if we have Info for the current tile
        // Insert new data if there is not current info
        // Do Nothing if there is current info
        // This should not be the case with a complete map as every ID should be in the system, consider removing? REMOVED 4.2.20
        // Perform the initial query
        $result = $db->query('SELECT *, UTC_TIMESTAMP() AS today FROM ' . $uni . '_Maps WHERE id = ' . $id);

        // Check for query errors
        if (!$result) {
            die("Query failed: " . $db->error);
        }

        // Counting SQL iterations per connection
        ++$sqlcount;

        // Fetch the first row
        $r = $result->fetch_object();

        // Check if any row was returned
        if (!$r) {
            // There is no existing information for the current tile
            if ($debug) {
                echo $id . ' New Information Inserting into DB<br>';
            }

            // Call method to add new map
            $dbClass->addMap($uni, $temp[$r_bg], $id, 0);

            // Counting SQL iterations per connection
            ++$sqlcount;

            // Perform the query again after adding new map
            $result = $db->query('SELECT *, UTC_TIMESTAMP() AS today FROM ' . $uni . '_Maps WHERE id = ' . $id);

            // Check for query errors
            if (!$result) {
                die("Query failed: " . $db->error);
            }

            // Counting SQL iterations per connection
            ++$sqlcount;

            // Fetch the updated row
            $r = $result->fetch_object();
        }

        // Free the result set
        $result->free();


        if ($debug) print_r($r);
        if ($debug) echo '<br>';

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
                $dbClass->addBuilding($uni, $temp[$r_fg], $id, 0);
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
                        $result = $db->query('SELECT * FROM ' . $uni . '_Maps WHERE fg = \'' . $temp[$r_fg] . '\' AND cluster = \'' . $r->cluster . '\'');

                        // Check for query errors
                        if (!$result) {
                            die("Query failed: " . $db->error);
                        }

                        // Counting SQL iterations per connection
                        ++$sqlcount;

                        // Initialize an array to hold the results
                        $gems = [];

                        // Fetch each row as an object
                        while ($g = $result->fetch_object()) {
                            $gems[] = $g;
                        }

                        // Free the result set
                        $result->free();

                        // Process each gem
                        foreach ($gems as $g) {
                            $dbClass->removeBuilding($uni, $g->id, 0);
                            // Counting SQL iterations per connection
                            ++$sqlcount;
                        }
                    }
                    if ($debug) {
                        echo $id . ' Deleting Old Building<br>';
                    }
                    $dbClass->removeBuilding($uni, $id, 0);
                    ++$sqlcount; // Counting SQL iterations per connection
                    if ($debug) {
                        echo $id . ' Inserting New Building<br>';
                    }
                    $dbClass->addBuilding($uni, $temp[$r_fg], $id, 0);
                    ++$sqlcount; // Counting SQL iterations per connection
                } else {
                    if ($debug) echo $id . ' Foreground info Matches DB<br>';
                    $dbClass->updateMapFG($uni, $temp[$r_fg], $id);
                    ++$sqlcount; // Counting SQL iterations per connection
                    if ($temp[$r_fg] != $r->fg) {
                        if ($debug) echo $id . ' Foreground Image Changed<br>';
                        $db->query('UPDATE ' . $uni . '_Buildings set image = \'' . $temp[$r_fg] . '\' WHERE id = ' . $id);
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
                    $dbClass->removeBuilding($uni, $id, 1);
                    ++$sqlcount; // Counting SQL iterations per connection
                } else {
                    if ($debug) echo 'Blowing Away a building';
                    $dbClass->removeBuilding($uni, $id, 0);
                    ++$sqlcount; // Counting SQL iterations per connection
                }
            }
            if ($debug) echo $id . ' Checking NPC data<br>';
            if (in_array($id, $static)) {
                continue;
            }
            if ($debug) echo $id . ' Not Static NPC<br>';
            if (is_null($r->npc)) {
                if ($debug) echo $id . ' No NPC Data in DB<br>';
                if (is_null($r->fg) || $temp[2]) { //Add check that we say the FG is not a building but the DB might have something?
                    //if (is_null($r->fg)||$r_fg == 0) { //Added check that we say the FG is not a building but the DB might have something
                    if ($debug) echo $id . ' setting $npc = ' . $temp[$r_npc] . ' and then' . ' Adding New NPC<br>';
                    $npc = $temp[$r_npc];
                    if (in_array($npc, $hack)) {
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $db->query("SELECT * FROM {$uni}_Users WHERE `ip` = '$ip'");
                        ++$sqlcount; // Counting SQL iterations per connection
                        //if ($user = $dbClass->nextObject()) { //removed in debugging 9.16.24
                        //not sure what this was for but it won't work in current instance as the DB fields have changed
                        //$db->query("INSERT INTO ${uni}_Hack (`id`,`username`,`version`,`image`,`date`) VALUES ($user->id,'$user->username','$version','$npc',$UTC_TIMESTAMP())");
                        ++$sqlcount; // Counting SQL iterations per connection
                        //}
                    }
                    if (in_array($temp[$r_npc], $single)) {
                        // NPC is only one per Cluster Find location of previous Instance
                        $db->query("SELECT * FROM {$uni}_Maps WHERE cluster = '$r->cluster' AND npc = '$npc'");
                        ++$sqlcount; // Counting SQL iterations per connection
                        while ($t = $dbClass->nextObject()) {
                            $to_delete[] = $t->id;
                        }
                        if ($to_delete) {
                            for ($t = 0; $t < sizeof($to_delete); $t++) {
                                // Delete NPC from Data Table
                                if ($debug) echo 'Blow Away 1';
                                $dbClass->removeNPC($uni, $to_delete[$t]);
                                ++$sqlcount; // Counting SQL iterations per connection
                            }
                        }
                    }
                    $nid = 0;
                    if (isset($temp[2])) {
                        $nid = $temp[2];
                    }
                    if ($debug) echo 'Adding NPC with nid' . ($uni . $temp[$r_npc] . $id . $nid) . '<br>';
                    $dbClass->addNPC($uni, $temp[$r_npc], $id, " ", 0, 0, $nid); // Adding nid
                    ++$sqlcount; // Counting SQL iterations per connection
                }
            } elseif ($temp[$r_npc] == $r->npc) { // it's the same NPC the map shows
                if ($debug) echo $id . ' with nid = ' . $temp[2] . ' and Uncloaked<br>';
                if (isset($temp[2])) {
                    $dbClass->updateMapNPC($uni, $temp[$r_npc], $id, 0, $temp[2]);
                } else {
                    // Handle the case where $temp[2] is not set
                    $dbClass->updateMapNPC($uni, $temp[$r_npc], $id, 0); // Adding nid if known as well
                    echo "Error: \$temp[2] is not set.";
                }
                ++$sqlcount; // Counting SQL iterations per connection
            } else {
                if ($debug) echo $id . ' Has a Different NPC so Blow Away 2<br>';
                $dbClass->removeNPC($uni, $id);
                ++$sqlcount; // Counting SQL iterations per connection
                $dbClass->addNPC($uni, $temp[$r_npc], $id, "", 1, $temp[2] ?? null); // Adding nid
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
                    $dbClass->removeBuilding($uni, $id, 1);
                    ++$sqlcount; // Counting SQL iterations per connection
                } else {
                    $dbClass->removeBuilding($uni, $id, 0);
                    ++$sqlcount; // Counting SQL iterations per connection
                }
                //}
            }
            if (!(is_null($r->npc))) { // Check if NPC exists
                if ($debug) {
                    if (($id == $_REQUEST['id']) && (isset($temp[2]) && $temp[2] !== false) && (str_contains($temp[1], "ships")) && !(in_array($r->npc, $nonblocking))) {
                        echo '--result of test for Blow Away 3';
                    }
                }

                if (($id == $_REQUEST['id']) && (isset($temp[2]) && $temp[2] !== false) && (str_contains($temp[1], "ships")) && !(in_array($r->npc, $nonblocking))) {
                    if ($debug) echo 'is this working or what<br>';
                    if ($debug) echo '??NPC has been killed<br>';
                    if ($debug) echo 'Blow Away 3<br>';
                    $dbClass->removeNPC($uni, $id);
                    ++$sqlcount; // Counting SQL iterations per connection
                } else {
                    if ((in_array($r->npc, $cloaked)) && !(in_array($r->npc, $mobile))) {
                        if ($debug) echo 'NPC has cloaked<br>';
                        if (is_null($r->npc_cloaked)) {
                            $dbClass->updateMapNPC($uni, $temp[$r_npc], $id, 1, $temp[2] ?? null);
                            ++$sqlcount; // Counting SQL iterations per connection
                        } else {
                            $show = strtotime($r->today) - strtotime($r->npc_updated);
                            if ($show > 432000) {
                                if ($debug) echo 'Blow Away 4<br>';
                                $dbClass->removeNPC($uni, $id);
                                ++$sqlcount; // Counting SQL iterations per connection
                            }
                        }
                    } else {
                        if ($debug) echo '???NPC has been killed<br>';
                        if ($debug) echo $id . '-' . $_REQUEST['id'];
                        if ($debug) echo '???Now Trying to remove the record<br>';
                        if ($debug) echo 'Blow Away 5<br>';
                        $dbClass->removeNPC($uni, $id);
                        ++$sqlcount; // Counting SQL iterations per connection
                    }
                }
            }
        }
    }
}
//}
//$db->query("INSERT INTO connection_log (`universe`,`username`,`user_id`,`querycount`,`duration`, `date`) VALUES ('Test','Test2','5','5','1.1',UTC_TIMESTAMP())");
//	$db->query("INSERT INTO connection_log (`universe`,`username`,`user_id`,`querycount`,`duration`, `date`) VALUES ('$uni','$user','$uid','$sqlcount','$exec_time',UTC_TIMESTAMP())");
++$sqlcount;
$time_post = microtime(true);
$exec_time = $time_post - $time_pre;
$db->query("INSERT INTO connection_log (`universe`,`username`,`user_id`,`querycount`,`duration`, `date`, `payload`) VALUES ('$uni','$user','$uid','$sqlcount','$exec_time',UTC_TIMESTAMP(),'$dataString')");
$db->close();
$db = null;
