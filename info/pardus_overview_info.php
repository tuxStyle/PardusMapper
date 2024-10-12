<?php
declare(strict_types=1);
require_once('../app/settings.php');

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Request;
use Pardusmapper\DB;
use Pardusmapper\CORS;

/** @var string $img_url */

CORS::pardus();

debug($_REQUEST);

// Set Univers Variable and Session Name
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::OK, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

$id = Request::pint(key: 'id');
http_response(is_null($id), ApiResponse::OK, sprintf('id query parameter is required or invalid: %s', $id ?? 'null'));

$b_loc = DB::building(id: $id, universe: $uni);
$m = DB::map(id: $id, universe: $uni);

// REVIEW
// the original query here was not ignoring the deleted NPCs
// 'SELECT *, UTC_TIMESTAMP() "today"  FROM ' . $uni . '_Test_Npcs WHERE id = ' . $id
// TODO: check if we can ignore the deleted NPCs, we sprobably should as they are not on the pardus mao
$npc_loc = DB::npc(id: $id, universe: $uni, excludeDeleted: false);

$return = '';

if ($b_loc) {
    $loc = $b_loc;
    debug('Building: ' . $id);

    $stock = [];

    //Get Resource Data
    [$res_img, $res_id] = DB::res_data_static();

    if (strpos($loc->image,"planet") || strpos($loc->image,"starbase") || strpos($loc->image,"outpost")) {
        // Get Stocking Info for TOs/Planets/SBs
        $stocks = DB::stocks(id: $id, universe: $uni, nonZero: true);
    } else {
        // Get Stocking Information
        $stocks = DB::stocks(id: $id, universe: $uni);
    }
    
    foreach($stocks as $q) { $stock[$res_id[$q->name]] = $q; }
    // Make sure the Stock is in the correct order
    if ($stock) { ksort($stock, SORT_NUMERIC); }


    //Calculate Ticks Passed
    $format = '%F %T';
    
    $tick = 0;
    if ($loc->stock_updated) {
        $ts = strtotime($loc->stock_updated);
        $date = new DateTime("@$ts");
        $date->setTime(1,25,0);
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

    // Calculate Days/Hours/Mins Since last Stock Update
    $diff['string'] = 'NEVER';
    if ($loc->stock_updated) {
        $diff['sec'] = strtotime($loc->today) - strtotime($loc->stock_updated);
        $diff['days'] = $diff['sec']/60/60/24;
        $diff['hours'] = ($diff['days'] - floor($diff['days'])) * 24;
        $diff['min'] = ($diff['hours'] - floor($diff['hours'])) * 60;
        $diff['string'] = floor($diff['days']) . 'd ' . floor($diff['hours']) . 'h ' . floor($diff['min']) . 'm';
    }

    $row = 8;
    $i = 0;

    $return .= '<table class="messagestyle" width="210">';
    $return .= '<tr style="background-color:#003040;"><td colspan="' . $row . '" align="center">' . $loc->name . ' [' . $loc->x . ',' . $loc->y . ']</td></tr>';
    $return .= '<tr style="background-color:#003040;">';
    $return .= '<td colspan="' . floor($row/2) . '">Stock Last Updated:</td>';
    $return .= '<td colspan="' . ceil($row/2) . '" align="right">' . $diff['string'] . '</td>';
    $return .= '</tr>';
    $return .= '<tr class="header">';
    $return .= '<th colspan="2" style="background-color:#500000; color:#BBBBDD;">Resource</th>';
    $return .= '<th style="background-color:#330033; color:#BBBBDD;">Amount<br>Available</th>';

    if (strpos($loc->image,"planet") || strpos($loc->image,"starbase")) {
        $return .= '<th style="background-color:#000000; color:#BBBBDD;">Bal</th>';
    }
    $return .= '<th style="background-color:#000000; color:#BBBBDD;">Amount<br>Needed</th>';
    $return .= '<th style="background-color:#505000; color:#BBBBDD;">Price (Buy)</th>';
    $return .= '<th style="background-color:#505000; color:#BBBBDD;">Price (Sell)</th>';
    //if (!(strpos($loc->image,"planet") || strpos($loc->image,"starbase"))) { $return .= '<th style="background-color:#330033; color:#BBBBDD;">Need</th>'; }
    $return .= '</tr>';

    if ($stock) {
        foreach ($stock as $s) {
            $return .=  ($i++ % 2 != 0) ? '<tr class="alternating normal">' : '<tr class="normal">';
            $return .= '<td><img src="' . $img_url . $res_img[$s->name] . '" height="8" width="8" alt=""></td>';
            $return .= '<td><font color="#009900"><strong>' . $s->name . '</strong></td>';
            $amount = $s->amount - $s->min;
            if ($amount < 0) $amount = 0;
            $return .= '<td align="right">' . number_format((int)$amount) .'</td>';
            if (strpos($loc->image,"planet") || strpos($loc->image,"starbase")) {
                $return .= '<td align="right">';
                if($s->bal != 0) {
                    if ($s->bal > 0) { $return .= '<font color="#009900"><strong>+' . number_format((int)$s->bal) . '</strong></font>'; }
                    else { $return .= '<font color="#FFAA00"><strong>' . number_format((int)$s->bal) . '</strong></font>'; }
                } else { $return .= number_format((int)$s->bal); }
                $return .= '</td>';
            }
            $amount = $s->max - $s->amount;
            if ($amount < 0) $amount = 0;
            $return .= '<td align="right">' . number_format((int)$amount) . '</td>';
            $return .= '<td align="right">' . number_format((int)$s->buy) . '</td>';
            $return .= '<td align="right">' . number_format((int)$s->sell) . '</td>';
            //if (!(strpos($loc->image,"planet") || strpos($loc->image,"starbase"))) {
            //	$return .= (($s->max - $s->amount) > 0) ? '<td align="right">' . number_format($s->max - $s->amount) . '</td>' : '<td align="right">0</td>';
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
    debug('Npc: ' . $id);

    $npc = DB::npc_static(name: $loc->name);
    debug($npc);
    
    $return .= '<table class="messagestyle" width="210">';
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

if (!($b_loc || $npc_loc) && $m) {
    debug('Not NPC or Building: ' . $id);
    // Calculate Days/Hours/Mins Since Last viewed on Nav
    $map = '-d -h -m';
    if ($m->fg_updated) {
        $diff['sec'] = strtotime($m->today) - strtotime($m->fg_updated);
        $diff['days'] = $diff['sec']/60/60/24;
        $diff['hours'] = ($diff['days'] - floor($diff['days'])) * 24;
        $diff['min'] = ($diff['hours'] - floor($diff['hours'])) * 60;
        $map = floor($diff['days']) . 'd ' . floor($diff['hours']) . 'h ' . floor($diff['min']) . 'm';
    }

    $return .= '<table class="messagestyle" width="210">';
    $return .= '<tr style="background-color:#003040;">';
    $return .= '<td align="left">Map Last Updated:</td>';
    $return .= '<td align="right">' . $map . '</td>';
    $return .= '</tr>';
    
    $return .= '<tr><td align="center" colspan="2"><h3>No Info in DB</h3></td></tr></table>';
}
echo $return;
