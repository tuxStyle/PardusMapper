<?php 
declare(strict_types=1);
require_once('../app/settings.php');

use Pardusmapper\Core\Settings;
use Pardusmapper\Core\MySqlDB;

header('Access-Control-Allow-Origin: ' . Settings::$BASE_URL);

$dbClass = new MySqlDB();  // Create an instance of the Database class
$db = $dbClass->getDb();    // Get the mysqli connection object

$uni = isset($_POST['uni']) && !empty($_POST['uni']) ? $db->real_escape_string($_POST['uni']) : null;
$sector = isset($_POST['sector']) && !empty($_POST['sector']) ? $db->real_escape_string($_POST['sector']) : null;
$cluster = isset($_POST['cluster']) && !empty($_POST['cluster']) ? $db->real_escape_string($_POST['cluster']) : null;
$img_url = isset($_POST['img_url']) && !empty($_POST['img_url']) ? $db->real_escape_string($_POST['img_url']) : null;
$mode = isset($_POST['mode']) && !empty($_POST['mode']) ? $db->real_escape_string($_POST['mode']) : null;
$shownpc = isset($_POST['shownpc']) && !empty($_POST['shownpc']) ? $db->real_escape_string($_POST['shownpc']) : null;
$grid = isset($_POST['grid']) && !empty($_POST['grid']) ? $db->real_escape_string($_POST['grid']) : null;
$whole = isset($_POST['whole']) && !empty($_POST['whole']) ? $db->real_escape_string($_POST['whole']) : null;
$loc = isset($_POST['id']) && !empty($_POST['id']) ? $db->real_escape_string($_POST['id']) : null; //trying to figure out why $loc is missing definition


// Start the Session
session_name((string)$uni);
session_start();

$security = 0;
if (isset($_SESSION['security'])) { $security = $db->real_escape_string($_SESSION['security']); }

$coreWH = ['Pardus', 'Enif', 'Quaack', 'Nhandu', 'Procyon'];
if (in_array($sector, $coreWH)) {
    $dbClass->pardusWHStatus((string)$uni); // Call the method on the mysqldb instance
}
	
// Get Sector Information
$result = $db->query('SELECT * FROM Pardus_Sectors WHERE name = \'' . $sector . '\'');
$s = $result->fetch_object();
$result->free();
// throw_when(is_null($s), sprintf('Sector(%s) not found!', $sector));

// NPCs only shown to logged in users?
$npc_list[] = 'opponents/energy_sparker.png';
$npc_list[] = 'opponents/smuggler_escorted.png';
$npc_list[] = 'opponents/euryale.png';
$npc_list[] = 'opponents/euryale_swarmlings.png';
$npc_list[] = 'opponents/pirate_famous.png';
$npc_list[] = 'opponents/hidden_drugstash.png';
$npc_list[] = 'opponents/smuggler_lone.png';
$npc_list[] = 'opponents/medusa.png';
$npc_list[] = 'opponents/medusa_swarmling.png';
$npc_list[] = 'opponents/solar_banshee.png';
$npc_list[] = 'opponents/stheno.png';
$npc_list[] = 'opponents/stheno_swarmling.png';
$npc_list[] = 'opponents/energybees.png';
$npc_list[] = 'opponents/x993_battlecruiser.png';
$npc_list[] = 'opponents/x993_mothership.png';
$npc_list[] = 'opponents/z15_fighter.png';
$npc_list[] = 'opponents/z15_repair_drone.png';
$npc_list[] = 'opponents/z15_scout.png';
$npc_list[] = 'opponents/z15_spacepad.png';
$npc_list[] = 'opponents/z16_fighter.png';
$npc_list[] = 'opponents/z16_repair_drone.png';
//$npc_list[] = 'opponents/vyrex_assassin.png';
//$npc_list[] = 'opponents/vyrex_larva.png';
//$npc_list[] = 'opponents/vyrex_mutant_mauler.png';
//$npc_list[] = 'opponents/vyrex_stinger.png';
//$npc_list[] = 'opponents/vyrex_hatcher.png';

// Get Map Data for Sector
echo $uni;
$result = $db->query('SELECT *, UTC_TIMESTAMP() as today FROM `' . $uni . '_Maps` WHERE sector = \'' . $sector . '\' AND starbase = 0 ORDER BY x,y');
$m = [];
while ($q = $result->fetch_object()) {
    $m[$q->x][$q->y] = $q;
    // $m[$q->id] = $q; // Uncomment if you want to use id as the key
}


$showfg = 0;
if ($mode == 'all' || $_POST['mode'] == 'buildings') {
	$showfg = 1;
}

$shownpc = 0;
if ($mode == 'all' || $_POST['mode'] == 'npcs') {
	$shownpc=1;
}

$whole = 0;
if ($whole == 1 || $_POST['whole'] == 1) {
	$whole=1;
}

/*
//Get NPC Information for Sector
*/

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
					if (!empty($map) && !empty($map->today) && !empty($map->fg_updated)) {
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
$result->free();
$dbClass->close();
echo $return;
//echo htmlentities($return);
