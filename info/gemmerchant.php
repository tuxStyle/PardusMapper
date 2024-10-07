<?php
declare(strict_types=1);
require_once('../app/settings.php');

/** @var string $base_url */
/** @var string $img_url */

use Pardusmapper\Core\MySqlDB;
use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Request;
use Pardusmapper\DB;
use Pardusmapper\CORS;
use Pardusmapper\Session;

CORS::mapper();

$db = MySqlDB::instance();

// Set Univers Variable and Session Name
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::OK, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

session_name($uni);
session_start();

$security = Session::pint(key: 'security', default: 0);


$building = [];
$sector = [];
$gem = [];

$db->execute(sprintf('SELECT *,  UTC_TIMESTAMP() "today" FROM %s_Maps WHERE fg LIKE ? and fg_spotted > (UTC_TIMESTAMP() - INTERVAL 30 DAY)', $uni), [
    's', '%gem_merchant%'
]);
while ($b = $db->nextObject()) { $building[] = $b; }

$sector = DB::sectors_static();
    
foreach ($building as $b) {
    foreach ($sector as $s) {
        $start = $s->s_id;
        $end = $start + ($s->rows * $s->cols);
        if ($start <= $b->id && $b->id <= $end) {
            $c = DB::cluster(id: $s->c_id);
            
            $gem[$b->id][0] = $b;
            $gem[$b->id][1] = $s;
            $gem[$b->id][2] = $c;
        }
    }
}

$db->close();

$return = '<table>';
$return .= '<tr>';
$return .= '<th>Cluster</th>';
$return .= '<th>Sector</th>';
$return .= '<th>Location</th>';
$return .= '<th>Merchant</th>';
$return .= '<th>Last Spotted</th>';
$return .= '</tr>';

$i = 0;
foreach ($gem as $key => $g) {
    $c = $g[2];
    $s = $g[1];
    $g = $g[0];
                
    // Calculate Days/Hours/Mins Since last Visited
    $diff['sec'] = strtotime($g->today) - strtotime($g->fg_updated);
    $diff['days'] = $diff['sec']/60/60/24;
    $diff['hours'] = ($diff['days'] - floor($diff['days'])) * 24;
    $diff['min'] = ($diff['hours'] - floor($diff['hours'])) * 60;
    $diff['string'] = floor($diff['days']) . 'd ' . floor($diff['hours']) . 'h ' . floor($diff['min']) . 'm';

    if ($i++ % 2 == 0) {
        $return .= '<tr class="alternating">';
    } else {
        $return .= '<tr>';
    }
    $return .= '<td align="center">';
        $return .= '<a href="' . $base_url . '/' . $uni . '/' . $c->code . '">'. $g->cluster . '</a>';
    $return .= '</td>';
    $return .= '<td align="center">';
        $return .= '<a href="' . $base_url . '/' . $uni . '/' . $g->sector . '">' . $g->sector . '</a>';
    $return .= '</td>';
    $return .= '<td align="center">[' . $g->x . ',' . $g->y . ']</td>';
    $return .= '<td align="center">';
        $return .= '<a href="' . $base_url . '/' . $uni . '/' . $g->sector . '/' . $g->x . '/' . $g->y . '" />';
            $return .= '<img src="' . $img_url . $g->fg . '" onMouseOut="closeInfo();" onMouseOver="openInfo(\'' . $base_url . '\',\'' . $uni . '\',' . $key . ');"/>';
        $return .= '</a>';
    $return .= '</td>';
    $return .= '<td align="center">' . $diff['string'] . '</td>';
    $return .= '</tr>';
}
$return .= '</table>';
echo $return;
