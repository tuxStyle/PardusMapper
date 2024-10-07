<?php 
declare(strict_types=1);
require_once('../app/settings.php');

/** @var string $base_url */

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;
use Pardusmapper\CORS;
use Pardusmapper\Post;
use Pardusmapper\Session;
use Pardusmapper\DB;
use Pardusmapper\NPC;

CORS::mapper();

$dbClass = MySqlDB::instance();  // Create an instance of the Database class

// Set Univers Variable and Session Name
$uni = Post::uni();
http_response(is_null($uni), ApiResponse::OK, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

$sector = Post::pstring(key: 'sector');
http_response(is_null($sector), ApiResponse::OK, 'sector/s query parameter is required');

$cluster = Post::pstring(key: 'cluster');
$img_url = Post::pstring(key: 'img_url'); // this will override the settings/cookies value
$mode = Post::pstring(key: 'mode');
$shownpc = Post::pbool(key: 'shownpc'); // below we override it from $mode based on options
$whole = Post::pbool(key: 'whole'); // below we override it from $mode based on options
$grid = Post::pbool(key: 'grid');

$loc = Post::pint(key: 'id'); //trying to figure out why $loc is missing definition
http_response(is_null($loc), ApiResponse::OK, sprintf('location(loc) query parameter is required or invalid: %s', $loc ?? 'null'));

// Start the Session
session_name($uni);
session_start();

$security = Session::pint(key: 'security', default: 0);

$coreWH = ['Pardus', 'Enif', 'Quaack', 'Nhandu', 'Procyon'];
if (in_array($sector, $coreWH)) {
    DB::wh_update_pardus_status(universe: $uni); // Call the method on the mysqldb instance
}

// Get Sector Information
$s = DB::sector(sector: $sector);
http_response(is_null($s), ApiResponse::OK, sprintf('sector not found for sector name: %s', $sector)); // exit if not found in DB

// NPCs only shown to logged in users?
$npc_list = NPC::for_logged_users();

// Get Map Data for Sector
$dbClass->execute(sprintf('SELECT *, UTC_TIMESTAMP() as today FROM %s_Maps WHERE sector = ? AND starbase = 0 ORDER BY x,y', $uni), [
    's', $sector
]);
$m = [];
while ($q = $dbClass->fetchObject()) {
    $m[$q->x][$q->y] = $q;
    // $m[$q->id] = $q; // Uncomment if you want to use id as the key
}


$showfg = in_array($mode, ['all', 'buildings']) ? true : false;
$shownpc = in_array($mode, ['all', 'npcs']) ? true : $shownpc;

//Get NPC Information for Sector

$return = '<table id="sectorTableMap" >';
$return .= '<thead><tr><th />';
for ($i = 0;$i < $s->cols;$i++) { $return .= '<th>' . $i . '</th>'; }
$return .= '<th /></tr></thead>';
$return .= '<tbody>';
for ($y = 0;$y < $s->rows;$y++) {
    $return .= '<tr><th>' . $y . '</th>';
    for ($x = 0;$x < $s->cols;$x++) {
        if (isset($m[$x][$y])) {
            $map = $m[$x][$y]; 
            $return .= '<td id="' . $map->id . '"';
            if ($grid) { $return .= ' class="grid"'; }
            else { $return .= ' class="nogrid"'; }
            if (!$map->wormhole && (($showfg && $map->fg) || ($shownpc && $map->npc && (!in_array($map->npc,$npc_list) || isset($_SESSION['user']))))) {
                $return .= ' onClick="loadDetail(\'' . $base_url . '\',\'' . $uni . '\',' . $map->id . ');return true;" onMouseOut="closeInfo();" onMouseOver="openInfo(\'' . $base_url . '\',\'' . $uni . '\',' . $map->id . ');"';
            } 
            $return .= '>';

            // Set the Background
            $bg_img = $img_url . $map->bg;
            $return .= '<img class="bg" src="' . $bg_img . '" title=""/>';
            if (($map->security == 0) || ($security == $map->security) || ($security == 100)) {
                if ($map->fg){
                    $fg_img = $img_url . $map->fg;
                    // Calculate Days/Hours/Mins Since last Visited
                    //$fg_diff['sec'] = strtotime($map->today) - strtotime($map->fg_date);
                    if (!empty($map->today) && !empty($map->fg_updated)) {
                        $fg_diff['sec'] = strtotime($map->today) - strtotime($map->fg_updated);
                    } else {
                        echo "Invalid or missing date values.";
                        $fg_diff['sec'] = 0;
                    }
                    $fg_diff['days'] = $fg_diff['sec']/60/60/24;
                    $fg_diff['hours'] = ($fg_diff['days'] - floor($fg_diff['days'])) * 24;
                    $fg_diff['min'] = ($fg_diff['hours'] - floor($fg_diff['hours'])) * 60;
                    $fg_diff['string'] = ' ' . floor($fg_diff['days']) . 'd ' . floor($fg_diff['hours']) . 'h ' . floor($fg_diff['min']) . 'm';
                            
                    // Set Wormhole Data if we Got it
                    if ($map->wormhole && $security != 100) {
                        $return .= '<a href="'. $base_url .'/' . $uni . '/' . $map->wormhole .'">';
                        if (strpos($fg_img,"wormholeseal")) {
                            if (strpos($fg_img,"open")) {
                                $return .= '<img class="fg" src="' . $fg_img . '" alt="" title="' . $map->wormhole . ' {Open} [' . $fg_diff['string'] . ']" />';							
                            } else {
                                $return .= '<img class="fg" src="' . $fg_img . '" alt="" title="' . $map->wormhole . ' {Closed} [' . $fg_diff['string'] . ']" />';							
                            }
                        } else {
                            //$return .= '<img class="fg" src="' . $fg_img . '" alt="' . $loc . '" title=" ' . $map->wormhole . ' " />';
                            $return .= '<img class="fg" src="' . $fg_img . '" alt="' . $loc . '" title=" ' . $map->wormhole . ' " />';
                        }
                        $return .= '</a>';
                    // Set ability to delete Wormhole
                    }
                    else if ($map->wormhole && $security == 100) {
                        
                        if ($whole == 1){$return .= '<a href="'. $base_url .'/include/destroyWH.php?uni=' .$uni. '&id=' .$map->id. '" target="_blank">';}
                        else {$return .= '<a href="'. $base_url .'/' . $uni . '/' . $map->wormhole .'">';}
                        if (strpos($fg_img,"wormholeseal")) {
                            if (strpos($fg_img,"open")) {
                                $return .= '<img class="fg" src="' . $fg_img . '" alt="" title="' . $map->wormhole . ' {Open} [' . $fg_diff['string'] . ']" />';							
                            } else {
                                $return .= '<img class="fg" src="' . $fg_img . '" alt="" title="' . $map->wormhole . ' {Closed} [' . $fg_diff['string'] . ']" />';							
                            }
                        } else {
                            if ($whole == 1){$return .= '<img class="fg" src="' . $fg_img . '" alt="' . $loc . '" title="remove ID '.$map->id.'" />';}
                            else {$return .= '<img class="fg" src="' . $fg_img . '" alt="' . $loc . '" title="' . $map->wormhole . '" />';}
                        }
                        $return .= '</a>';
                    // Set Building Info if we got it
                    }
                    elseif ($showfg || strpos($fg_img,"planet") || strpos($fg_img,"federation"))  {
                        $return .= '<img class="fg" src="' . $fg_img . '" alt = "' . $fg_diff['string'] . '"';
                        /*
                        //if ($b = $building[$loc]) {
                            if (($b->security == 0) || ($security == $b->security) || ($security == 100)) {
                                $return .= ' onClick="loadDetail(\'' . $base_url . '\',\'' . $uni . '\',' . $loc . ');return true;" onMouseOut="closeInfo();" onMouseOver="openInfo(\'' . $base_url . '\',\'' . $uni . '\',' . $loc . ');"';
                                $return .= ' alt="' . $fg_diff['string'] . '"';
                            } else {
                                $return .= ' title="' . $fg_diff['string'] . '"';
                            }
                        // Else Just set the Foreground
                        //} else {
                            $return .= ' title="' . $fg_diff['string'] . '"';
                        //}
                        */
                        $return .= ' />';
                    }
                }
                if ($map->npc && $shownpc && !$map->wormhole) {
                    if (!in_array($map->npc,$npc_list) || isset($_SESSION['user'])) {
                        $npc_img = $img_url . $map->npc;
                        // Calculate Days/Hours/Mins Since last Visited
                        //$npc_diff['sec'] = strtotime($map->today) - strtotime($map->npc_date);
                        $npc_diff['sec'] = strtotime($map->today) - strtotime($map->npc_updated);
                        $npc_diff['days'] = $npc_diff['sec']/60/60/24;
                        $npc_diff['hours'] = ($npc_diff['days'] - floor($npc_diff['days'])) * 24;
                        $npc_diff['min'] = ($npc_diff['hours'] - floor($npc_diff['hours'])) * 60;
                        $npc_diff['string'] = ' ' . floor($npc_diff['days']) . 'd ' . floor($npc_diff['hours']) . 'h ' . floor($npc_diff['min']) . 'm';

                        // Pilot has logged Data recently
                        $npc_id = 'npc';
                        //if ($b = $building[$loc])  { $npc_id .= 'Small'; } //no idea when this stopped working, if it ever did 9.16.24
                        //else
                        if ($map->npc_cloaked == 1) { $npc_id .= 'Cloak'; }
                    
                        $return .= '<img class="' . $npc_id . '" src="' . $npc_img . '" alt="' . $npc_diff['string'] . '"';
                        /*
                        //if ($n = $npc[$loc] || $building[$loc]) { 
                            $return .= ' onClick="loadDetail(\'' . $base_url . '\',\'' . $uni . '\',' . $loc . ');return false;" onMouseOut="closeInfo();" onMouseOver="openInfo(\'' . $base_url . '\',\'' . $uni . '\',' . $loc . ');"';
                            $return  .= ' alt="' . $npc_diff['string'] . '"';
                        //} else { 
                        //	$return .= ' title="' . $npc_diff['string'] . '"';
                        //}
                        */
                        $return .= ' />';
                    }
                }
            }
            $return .= '</td>';
        } else {
            if ($grid) { $return .= '<td class="grid">'; }
            else { $return .= '<td class="nogrid">'; }
            $return .= '<img class="bg" src="' . $img_url . 'backgrounds/energymax.png" title=""/></td>';								
        }
    }
    $return .= '<th>' . $y . '</th></tr>';
}
$return .= '</tbody>';
$return .= '<tfoot><tr><th />';
for ($i = 0;$i < $s->cols;$i++) { $return .= '<th>' . $i . '</th>'; }
$return .= '<th /></tr></tfoot></table>';

echo $return;
//echo htmlentities($return);
