<?php 
declare(strict_types=1);
require_once('../app/settings.php');

/** @var string $img_url */

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\CORS;
use Pardusmapper\Post;
use Pardusmapper\DB;
use Pardusmapper\Session;

CORS::mapper();

// Set Univers Variable and Session Name
$uni = Post::uni();
http_response(is_null($uni), ApiResponse::OK, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

$id = Post::pint(key: 'id');
http_response(is_null($id), ApiResponse::OK, sprintf('location(id) query parameter is required or invalid: %s', $loc ?? 'null'));

session_name($uni);
session_start();

$security = Session::pint(key: 'security', default: 0);

//$war_status = $db->query('SELECT WarStatus FROM War_Status WHERE Universe = \'' . $uni.'\'');

$b_loc = DB::building(id: $id, universe: $uni);
$npc_loc = DB::npc(id: $id, universe: $uni);
$m = DB::map(id: $id, universe: $uni);


$return = '';

if ($b_loc) {
    $loc = $b_loc;
    debug($loc);

    //Get Resource Data
    [$res_img, $res_id] = DB::res_data_static();

    // Get Stocking Information
    $stock = [];
    // REVIEW:
    // the original code used war status to filter stocks but, i'm missing data in the War_Status table so, disable this for now
    // TODO: check later
    $stocks = DB::stocks(id: $id, universe: $uni, warStatus: false);
    foreach($stocks as $q) { $stock[$res_id[$q->name]] = $q; }

    // Make sure the Stock is in the correct order
    if ($stock) { ksort($stock,SORT_NUMERIC); }


    //Calculate Ticks Passed
    $format = '%F %T';
    //$date = getdate(strtotime($loc->stock_updated));
    //$tick = mktime(1,25,0,$date['mon'],$date['mday'],$date['year']);
    
    $tick = 0;
    if ($loc->stock_updated) {
        $ts = strtotime($loc->stock_updated);
        $date = new DateTime("@$ts");
        //$date->setTimestamp(strtotime($loc->stock_updated));
        $date->setTime(1,25,0);
        //$tick = $date->getTimestamp();
        $tick = $date->format('U');

        while ($tick < strtotime($loc->stock_updated)) {
            $tick += (60 * 60 * 6);
        }
    }

    $count = 0;
    while ($tick < strtotime($loc->today)) {
        $tick += (60*60*6);
        $count++;
    }
    $tick = $count;

    $fs = $loc->freespace;

    $ticks_used = 100;

    // Calculate Days/Hours/Mins Since Last viewed on Nav
    $diff['sec'] = strtotime($m->today) - strtotime($m->fg_updated);
    $diff['days'] = $diff['sec']/60/60/24;
    $diff['hours'] = ($diff['days'] - floor($diff['days'])) * 24;
    $diff['min'] = ($diff['hours'] - floor($diff['hours'])) * 60;
    $map = floor($diff['days']) . 'd ' . floor($diff['hours']) . 'h ' . floor($diff['min']) . 'm';
    
    // Calculate Days/Hours/Mins Since Last viewed on Building Info
    $diff['sec'] = strtotime($loc->today) - strtotime($loc->updated);
    $diff['days'] = $diff['sec']/60/60/24;
    $diff['hours'] = ($diff['days'] - floor($diff['days'])) * 24;
    $diff['min'] = ($diff['hours'] - floor($diff['hours'])) * 60;
    $visited = floor($diff['days']) . 'd ' . floor($diff['hours']) . 'h ' . floor($diff['min']) . 'm';
    
    // Calculate Days/Hours/Mins Since last Stock Update
    $diff['sec'] = $loc->stock_updated ? strtotime($loc->today) - strtotime($loc->stock_updated) : 0;
    $diff['days'] = $diff['sec']/60/60/24;
    $diff['hours'] = ($diff['days'] - floor($diff['days'])) * 24;
    $diff['min'] = ($diff['hours'] - floor($diff['hours'])) * 60;
    $diff['string'] = floor($diff['days']) . 'd ' . floor($diff['hours']) . 'h ' . floor($diff['min']) . 'm';

    $row = 8;
    $i = 0;

    $return .= '<table id="overviewTable">';
    $return .= '<tr style="background-color:#003040;"><td colspan="' . $row . '" align="center">' . $loc->name . ' [' . $loc->x . ',' . $loc->y . ']</td></tr>';
    $return .= '<tr style="background-color:#003040;">';
    $return .= '<td colspan="' . floor($row/2) . '">Map Last Updated:</td>';
    $return .= '<td colspan="' . ceil($row/2) . '" align="right">' . $map . '</td>';
    $return .= '</tr>';
    $return .= '<tr style="background-color:#003040;">';
    $return .= '<td colspan="' . floor($row/2) . '">Building Last Updated:</td>';
    $return .= '<td colspan="' . ceil($row/2) . '" align="right">' . $visited . '</td>';
    $return .= '</tr>';
    $return .= '<tr style="background-color:#003040;">';
    $return .= '<td colspan="' . floor($row/2) . '">Stock Last Updated:</td>';
    $return .= '<td colspan="' . ceil($row/2) . '" align="right">' . $diff['string'] . '</td>';
    $return .= '</tr>';
    $return .= '<tr>';
    $return .= '<th colspan="2" style="background-color:#500000; color:#BBBBDD;">Resource</th>';
    if (strpos($loc->image,"starbase")) { $return .= '<th style="background-color:#330033; color:#BBBBDD;">Amount<br>Available</th>'; }
    else { 
        $return .= '<th style="background-color:#330033; color:#BBBBDD;">Amount</th>'; 
        if (strpos($loc->image,"planet")) {
        $return .= '<th style="background-color:#000000; color:#BBBBDD;">Bal</th>';
            if ($loc->owner) {
                $return .= '<th style="background-color:#000000; color:#BBBBDD;">Min</th>';
            }
        } else {
            $return .= '<th style="background-color:#000000; color:#BBBBDD;">Min</th>';
        }
    }

    if (strpos($loc->image,"starbase")) {
        $return .= '<th style="background-color:#000000; color:#BBBBDD;">Bal</th>';
    }
    if (strpos($loc->image,"starbase")) { $return .= '<th style="background-color:#000000; color:#BBBBDD;">Amount<br>Needed</th>'; }
    else { $return .= '<th style="background-color:#000000; color:#BBBBDD;">Max</th>'; }
    $return .= '<th style="background-color:#505000; color:#BBBBDD;">Price&nbsp;(Buy)</th>';
    $return .= '<th style="background-color:#505000; color:#BBBBDD;">Price&nbsp;(Sell)</th>';
    //if (!(strpos($loc->image,"planet") || strpos($loc->image,"starbase"))) { $return .= '<th style="background-color:#330033; color:#BBBBDD;">Needed</th>'; }
    $return .= '</tr>';

    if ($stock) {
        foreach ($stock as $s) {
            $return .=  ($i++ % 2 != 0) ? '<tr class="alternating">' : '<tr>';
            $return .= '<td><img src="' . $img_url . $res_img[$s->name] . '" height="8" width="8" alt=""></td>';
            $return .= '<td><font color="#009900"><strong>' . $s->name . '</strong></td>';
            if (strpos($loc->image,"starbase")) {
                $amount = $s->amount - $s->min;
                if ($amount < 0) $amount = 0;
                $return .= '<td align="right">' . number_format((int)$amount) .'</td>';
            } else { 
                $return .= '<td align="right">' . number_format((int)$s->amount) .'</td>'; 
                if (strpos($loc->image,"planet")) {
                    $return .= '<td align="right">';
                    if($s->bal != 0) {
                        if ($s->bal > 0) { $return .= '<font color="#009900"><strong>+' . number_format((int)$s->bal) . '</strong></font>'; }
                        else { $return .= '<font color="#FFAA00"><strong>' . number_format((int)$s->bal) . '</strong></font>'; }
                    } else { $return .= number_format((int)$s->bal); }
                    $return .= '</td>';
                    if ($loc->owner) { $return .= '<td align="right">' . number_format((int)$s->min) . '</td>'; }
                } else {
                    $return .= '<td align="right">' . number_format((int)$s->min) . '</td>';
                }
            }
        
            if (strpos($loc->image,"starbase")) {
                $return .= '<td align="right">';
                if($s->bal != 0) {
                    if ($s->bal > 0) { $return .= '<font color="#009900"><strong>+' . number_format((int)$s->bal) . '</strong></font>'; }
                    else { $return .= '<font color="#FFAA00"><strong>' . number_format((int)$s->bal) . '</strong></font>'; }
                } else { $return .= number_format((int)$s->bal); }
                $return .= '</td>';
            }
            if (strpos($loc->image,"starbase")) {
                $amount = $s->max - $s->amount;
                if ($amount < 0) $amount = 0;
                $return .= '<td align="right">' . number_format((int)$amount) . '</td>';
            } else { $return .= '<td align="right">' . number_format((int)$s->max) . '</td>'; }
            $return .= '<td align="right">' . number_format((int)$s->buy) . '</td>';
            $return .= '<td align="right">' . number_format((int)$s->sell) . '</td>';
            //if (!(strpos($loc->image,"planet") || strpos($loc->image,"starbase"))) {
            //	$return .= (($s->max - $s->amount) > 0) ? '<td align="right">' . number_format((int)$s->max - $s->amount) . '</td>' : '<td align="right">0</td>';
            //}	
        }
    }
    $return .= '</tr>';
    $return .= '<tr><td colspan="' . $row . '"><hr></td></tr>';
    $return .= '<tr style="background-color:#003040;">';
    $return .= '<td colspan="' . floor($row/2) . '">Free Space:</td>';
    $return .= '<td colspan="' .ceil($row/2) . '" align="right">' . number_format((int)$loc->freespace) . 't</td>';
    $return .= '</tr>';
    $return .= '<tr style="background-color:#003040;">';
    $return .= '<td colspan="' . floor($row/2) . '">Available Credits:</td>';
    $return .= '<td colspan="' . ceil($row/2) . '" align="right">' . number_format((int)$loc->credit) . '</td>';
    $return .= '</tr>';
    if (!strpos($loc->image,"outpost")) {
        $return .= '<tr style="background-color:#003040;">';
        $return .= '<td colspan="' . floor($row/2) . '">Ticks Past:</td>';
        $return .= '<td colspan="' . ceil($row/2) . '" align="right">';
        $return .= '<span id="ticks_passed">' . $tick . '</span>';
        $return .= '</td>';
        $return .= '</tr>';
    }
    $return .= '</table>';
    
    if ($npc_loc) { $return .= '<br>'; }
}

if ($npc_loc) {
    $row = 3;
    $loc = $npc_loc;
    $npc = DB::npc_static(name: $loc->name);
    $return .= '<table>';
    $return .= '<tr style="background-color:#003040;">';
    $return .= '<td colspan="' . $row . '" align="center">' . $loc->name . ' [' . $loc->x . ',' . $loc->y . ']</td>';
    $return .= '</tr>';
    $return .= '<tr>';
    $return .= '<td></td>';
    $return .= '<th style="background-color:#500000; color:#BBBBDD;">Reported</th>';
    $return .= '<th style="background-color:#505000; color:#BBBBDD;">Undamaged</th>';
    $return .= '</tr>';
    $return .= '<tr>';
    $return .= '<th>Hull</th>';
    if ($loc->hull != $npc->hull) { if (($loc->hull *= 2) == 600) { if ($loc->hull < $npc->hull) { $loc->hull = "600+"; } } }
    $return .= '<td align="center">' . $loc->hull . '</td>';
    $return .= '<td align="center">' . $npc->hull . '</td>';
    $return .= '</tr>';
    $return .= '<tr>';
    $return .= '<th>Armor</th>';
    if ($loc->armor != $npc->armor) { if (($loc->armor *= 2) == 600) { if ($loc->armor < $npc->armor) { $loc->armor = "600+"; } } }
    $return .= '<td align="center">' . $loc->armor . '</td>';
    $return .= '<td align="center">' . $npc->armor . '</td>';
    $return .= '</tr>';
    $return .= '<tr>';
    $return .= '<th>Shield</th>';
    if ($loc->shield != $npc->shield) { if (($loc->shield *= 2) == 600) { if ($loc->shield < $npc->shield) { $loc->shield = "600+"; } } }
    $return .= '<td align="center">' . $loc->shield . '</td>';
    $return .= '<td align="center">' . $npc->shield . '</td>';
    $return .= '</tr>';
    
        // Calculate Days/Hours/Mins Since last Visited
    $diff['sec'] = strtotime($loc->today) - strtotime($loc->spotted);
    $diff['days'] = $diff['sec']/60/60/24;
    $diff['hours'] = ($diff['days'] - floor($diff['days'])) * 24;
    $diff['min'] = ($diff['hours'] - floor($diff['hours'])) * 60;
    $diff['string'] = floor($diff['days']) . 'd ' . floor($diff['hours']) . 'h ' . floor($diff['min']) . 'm';

    $return .= '<tr style="background-color:#003040;">';
    $return .= '<td colspan="' . floor($row/2) . '">First Spotted:</td>';
    $return .= '<td colspan="' . ceil($row/2) . '" align="right">' . $diff['string'] . '</td>';
    $return .= '</tr>';

        // Calculate Days/Hours/Mins Since last Visited
    $diff['sec'] = strtotime($loc->today) - strtotime($loc->updated);
    $diff['days'] = $diff['sec']/60/60/24;
    $diff['hours'] = ($diff['days'] - floor($diff['days'])) * 24;
    $diff['min'] = ($diff['hours'] - floor($diff['hours'])) * 60;
    $diff['string'] = floor($diff['days']) . 'd ' . floor($diff['hours']) . 'h ' . floor($diff['min']) . 'm';

    $return .= '<tr style="background-color:#003040;">';
    $return .= '<td colspan="' . floor($row/2) . '">Last Reported:</td>';
    $return .= '<td colspan="' . ceil($row/2) . '" align="right">' . $diff['string'] . '</td>';
    $return .= '</tr>';
    $return .= '</table>';
}

if (!($b_loc || $npc_loc)) {
    // Calculate Days/Hours/Mins Since Last viewed on Nav
    $diff['sec'] = strtotime($m->today) - strtotime($m->fg_updated);
    $diff['days'] = $diff['sec']/60/60/24;
    $diff['hours'] = ($diff['days'] - floor($diff['days'])) * 24;
    $diff['min'] = ($diff['hours'] - floor($diff['hours'])) * 60;
    $map = floor($diff['days']) . 'd ' . floor($diff['hours']) . 'h ' . floor($diff['min']) . 'm';

    $return = '<table>';
    $return .= '<tr style="background-color:#003040;">';
    $return .= '<td align="left">Map Last Updated:</td>';
    $return .= '<td align="right">' . $map . '</td>';
    $return .= '</tr>';
    
    $return .= '<tr><td align="center" colspan="2"><h3>No Info in DB</h3></td></tr></table>';
}

echo $return;
