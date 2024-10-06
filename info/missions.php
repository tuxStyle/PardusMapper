<?php
declare(strict_types=1);
require_once('../app/settings.php');

use Pardusmapper\Core\MySqlDB;
use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Post;
use Pardusmapper\Session;
use Pardusmapper\CORS;
use Pardusmapper\DB;

CORS::mapper();

debug($_POST);

$db = MySqlDB::instance();  // Create an instance of the Database class

$uni = Post::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

$faction = Post::pstring(key: 'faction');
$pilot = Post::pstring(key: 'pilot');
$pilot_s = Post::pstring(key: 'pilot_s');
$mission_filter = Post::pstring(key: 'type');
$sort = Post::pstring(key: 'sort', default: '');
$order = Post::pstring(key: 'order');
$mode = Post::pstring(key: 'mode');
$source_id = Post::pint(key: 'loc');
$sector = Post::pstring(key: 'sector');
$cluster = Post::pstring(key: 'cluster');

session_name($uni);
session_start();

$security = Session::pint(key: 'security', default: 0);
$comp = Session::pint(key: 'comp');
$rank = Session::pint(key: 'rank');

$cstart = 0;
if ($comp >= 2) {$cstart = $comp - 2; }
$cend = $comp + 2;

$rstart = 0;
if ($rank >= 2) {$rstart = $rank - 2; }
$rend = $rank + 2;


$return = '';
$sort_by = '';

if (strlen((string) $sort)) {
	$sort_by = ' ORDER BY ';
	for($i=0;$i<strlen((string) $sort);$i++) {
		switch(substr((string) $sort,$i,1)) {
			case "A" :
				$sort_by .= "cluster ";
				if ($order & 1) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "B" :
				$sort_by .= "sector ";
				if ($order & 2) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "C" :
				$sort_by .= "loc ";
				if ($order & 4) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "D" :
				$sort_by .= "type_img ";
				if ($order & 8) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "E" :
				$sort_by .= "amount ";
				if ($order & 16) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				$sort_by .= "hack ";
				if ($order & 16) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "F" :
				$sort_by .= "time ";
				if ($order & 32) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "G" :
				$sort_by .= "t_cluster ";
				if ($order & 64) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "H" :
				$sort_by .= "t_sector ";
				if ($order & 128) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "I" :
				$sort_by .= "t_loc ";
				if ($order & 256) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "J" :
				$sort_by .= "credits ";
				if ($order & 512) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "K" :
				$sort_by .= "updated ";
				if ($order & 1024) { $sort_by .= " DESC, "; }
				else { $sort_by .= " ASC, "; }
				break;
		}
	}
	$sort_by = substr($sort_by,0,-2);
}

$query = sprintf('SELECT *, UTC_TIMESTAMP() today FROM %s_Test_Missions where 1 = 1', $uni);
$bindType = [];
$bindValues = [];
if ($faction == 0) {
	$query .= ' and ((faction is NULL';
	if ($mode) {
		$query .= ' AND comp BETWEEN ? AND ?';
        $bindType[] = 'ii';
        $bindValues = array_merge($bindValues, [$cstart, $cend]);
	}
	$query .= ')';
	if ($pilot_s == 'tss' || $pilot_s == 'eps') {
		$query .= ' OR (faction LIKE ? ';
        $bindType[] = 's';
        $bindValues[] = '%' . $pilot_s . '%';
		if ($mode) {
			$query .= ' AND comp BETWEEN ? AND ?';
            $bindType[] = 'ii';
            $bindValues = array_merge($bindValues, [$cstart, $cend]);
		}
		$query .= ')';
	} else {
		$query .= ' OR (faction LIKE ?';
        $bindType[] = 's';
        $bindValues[] = '%' . $pilot . '%';
		if ($mode) {
			$query .= ' AND rank BETWEEN ? AND ?';
            $bindType[] = 'ii';
            $bindValues = array_merge($bindValues, [$rstart, $rend]);
		}
		$query .= ')';
		if ($pilot_s == 'eps') {
			$query .= ' OR (faction LIKE ?';
            $bindType[] = 's';
            $bindValues[] = '%' . $pilot_s . '%';
			if ($mode) {
				$query .= ' AND comp BETWEEN ? AND ?';
                $bindType[] = 'ii';
                $bindValues = array_merge($bindValues, [$cstart, $cend]);
			}
			$query .= ')';
		}
	}
	$query .= ')';
}
if ($faction == 1) {
	if (!isset($pilot)) { $query .= ' and (faction LIKE \'%Nothing%\''; }
	else {
		$query .= ' AND (faction LIKE ?';
        $bindType[] = 's';
        $bindValues[] = '%' . $pilot . '%';
		if ($mode) {
			if ($pilot_s == 'tss' || $pilot_s == 'eps') { 
                $query .= ' AND comp BETWEEN ? AND ?';
                $bindType[] = 'ii';
                $bindValues = array_merge($bindValues, [$cstart, $cend]);
            }
			else {
                $query .= ' AND rank BETWEEN ? AND ?'; 
                $bindType[] = 'ii';
                $bindValues = array_merge($bindValues, [$rstart, $rend]);
            }
		}
	}
	$query .= ')';
}	
if ($faction == 2) {
	$query .= ' and (faction IS NULL';
	if ($mode) { 
        $query .= ' AND comp BETWEEN ? AND ?';
        $bindType[] = 'ii';
        $bindValues = array_merge($bindValues, [$cstart, $cend]);
    }
	$query .= ')';
}
if ($faction == 3) {
	if (isset($pilot_s) == 0) { $query .= ' and (faction LIKE \'%Nothing%\''; }
	else {
		$query .= ' AND (faction LIKE ?';
        $bindType[] = 's';
        $bindValues[] = '%' . $pilot_s . '%';
		if ($mode) {
            $query .= ' AND comp BETWEEN ? AND ?';
            $bindType[] = 'ii';
            $bindValues = array_merge($bindValues, [$cstart, $cend]);
		}
	}
	$query .= ')';
}	
if (1 == 2) {
	$query .= ' and ((faction is NULL';
	if ($mode) {
        $query .= ' AND comp BETWEEN ? AND ?';
        $bindType[] = 'ii';
        $bindValues = array_merge($bindValues, [$cstart, $cend]);
	}
	$query .= ')';
	if ($pilot_s == 'tss' || $pilot_s == 'eps') {
		$query .= ' OR (faction LIKE ? ';
        $bindType[] = 's';
        $bindValues[] = '%' . $pilot_s . '%';
		if ($mode) {
            $query .= ' AND comp BETWEEN ? AND ?';
            $bindType[] = 'ii';
            $bindValues = array_merge($bindValues, [$cstart, $cend]);
		}
		$query .= ')';
	} else {
		$query .= ' OR (faction LIKE ?';
        $bindType[] = 's';
        $bindValues[] = '%' . $pilot . '%';
		if ($mode) {
            $query .= ' AND rank BETWEEN ? AND ?'; 
            $bindType[] = 'ii';
            $bindValues = array_merge($bindValues, [$rstart, $rend]);
		}
		$query .= ')';
		if ($pilot_s == 'eps') {
			$query .= ' OR (faction LIKE ?';
            $bindType[] = 's';
            $bindValues[] = '%' . $pilot_s . '%';
			if ($mode) {
                $query .= ' AND comp BETWEEN ? AND ?';
                $bindType[] = 'ii';
                $bindValues = array_merge($bindValues, [$cstart, $cend]);
			}
			$query .= ')';
		}
	}
	$query .= ')';
}
if (strtolower((string) $mission_filter) != 'all') {
	$query .= ' AND type = ?';
    $bindType[] = 's';
    $bindValues[] = $mission_filter;
}
if (isset($source_id)) {
	$query .= ' AND source_id = ?';
    $bindType[] = 'i';
    $bindValues[] = $source_id;
} elseif (isset($sector)) {
	$query .= ' AND sector = ?';
    $bindType[] = 's';
    $bindValues[] = $sector;
} elseif (isset($cluster)) {
	$query .= ' AND cluster = ?';
    $bindType[] = 's';
    $bindValues[] = $cluster;
}

$params = [];
$params[] = implode('', $bindType);
$params = array_merge($params, $bindValues);

if (strlen($sort_by)) { $query .= $sort_by; }

debug($query, $params);
$db->execute($query, $params);

$delete = [];
$mission = [];
while ($q = $db->nextObject()) {
    // debug($q);
	// Calculate Days/Hours/Mins Since last Visited
	$diff['sec'] = isset($q->updated) ? strtotime($q->today) - strtotime($q->updated) : 99999999;
	$diff['days'] = $diff['sec']/60/60/24;
	$diff['hours'] = ($diff['days'] - floor($diff['days'])) * 24;
	$diff['min'] = ($diff['hours'] - floor($diff['hours'])) * 60;
	$diff['string'] = floor($diff['days']) . 'd ' . floor($diff['hours']) . 'h ' . floor($diff['min']) . 'm';

	$q->tick = $diff['string'];
		
	if ($diff['days'] > 1) { $delete[] = $q->id; }
	else { $mission[] = $q; }
}

if (count($delete) > 0) { foreach ($delete as $d) { DB::mission_remove(universe: $uni, id: $d); } }

$return = '<table id="mission_table">';
$return .= '<tr>';
$return .= '<th rowspan="2">Faction</th>';
$return .= '<th rowspan="2">';
if (str_contains((string) $sort,"A"))  {
	if ($order & 1) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'A\');return false;">&nbsp;Cluster&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'A\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'A\');return false;">Cluster</a>';
}
$return .= '</th>';
$return .= '<th rowspan="2">';
if (str_contains((string) $sort,"B"))  {
	if ($order & 2) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'B\');return false;">&nbsp;Sector&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'B\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'B\');return false;">Sector</a>';
}
$return .= '</th>';
$return .= '<th rowspan="2">';
if (str_contains((string) $sort,"C"))  {
	if ($order & 4) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'C\');return false;">&nbsp;Location&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'C\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'C\');return false;">Location</a>';
}
$return .= '</th>';
$return .= '<th rowspan="2">';
if (str_contains((string) $sort,"D"))  {
	if ($order & 8) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'D\');return false;">&nbsp;Type&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'D\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'D\');return false;">Type</a>';
}
$return .= '</th>';
$return .= '<th rowspan="2">';
if (str_contains((string) $sort,"E"))  {
	if ($order & 16) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'E\');return false;">&nbsp;Amount&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'E\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'E\');return false;">Amount</a>';
}
$return .= '</th>';
$return .= '<th rowspan="2">';
if (str_contains((string) $sort,"F"))  {
	if ($order & 32) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'F\');return false;">&nbsp;Time&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'F\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'F\');return false;">Time</a>';
}
$return .= '</th>';
$return .= '<th colspan="3">Target</th>';
$return .= '<th rowspan="2">';
if (str_contains((string) $sort,"J"))  {
	if ($order & 512) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'J\');return false;">&nbsp;Reward&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'J\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'J\');return false;">Reward</a>';
}
$return .= '</th>';
$return .= '<th rowspan="2">';
if (str_contains((string) $sort,"K"))  {
	if ($order & 1024) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'K\');return false;">&nbsp;Loaded&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'K\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'K\');return false;">Loaded</a>';
}
$return .= '</th>';
$return .= '</tr>';
$return .= '<tr>';
$return .= '<th>';
if (str_contains((string) $sort,"G"))  {
	if ($order & 64) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'G\');return false;">&nbsp;Cluster&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'G\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'G\');return false;">Cluster</a>';
}
$return .= '</th>';
$return .= '<th>';
if (str_contains((string) $sort,"H"))  {
	if ($order & 128) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'H\');return false;">&nbsp;Sector&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'H\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'H\');return false;">Sector</a>';
}
$return .= '</th>';
$return .= '<th>';
if (str_contains((string) $sort,"I"))  {
	if ($order & 256) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'I\');return false;">&nbsp;Location&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'I\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'I\');return false;">Location</a>';
}
$return .= '</th>';
$return .= '</tr>';


$i = 0;

if ($mission) { foreach ($mission as $m) {
	if ($i++ % 2 == 0) {
		$return .= '<tr class="alternating">';
	} else {
		$return .= '<tr>';
	}
	if ($m->faction) {
		$return .= '<td align="center"><img src="' . $img_url . $m->faction . '" /></td>';
	} else { $return .= '<td align="center"> - </td>'; }
	$c = DB::cluster(code: $m->cluster);
	$return .= '<td align="center"><a href="' . $base_url . '/' . $uni . '/' . $m->cluster . '">' . $c->name . '</td>';
	$return .= '<td align="center"><a href="' . $base_url . '/' . $uni . '/' . $m->sector . '">' . $m->sector . '</a></td>';
	$return .= '<td align="center"><a href="' . $base_url . '/' . $uni . '/' . $m->sector . '/' . $m->x . '/' . $m->y . '">' . $m->loc . '<br>[' . $m->x . ',' . $m->y . ']</a></td>';
	$return .= '<td align="center"><img height="32" src="' . $img_url . $m->type_img . '" title="' . $m->type . '"/></td>';
	if ($m->amount) { $return .= '<td align="center">' . $m->amount . '</td>'; }
	elseif ($m->hack) { 
		if (stripos($m->hack,"<br>")) { 
			$xp = explode("<br>",$m->hack);
			$return .= '<td align="center">' . $xp[0] . '</td>';
		} else { $return .= '<td align="center"><img src="' . $img_url . '/' . $m->hack . '" /></td>'; }
	}
	else { $return .= '<td align="center"> - </td>'; }
	if ($m->time) { $return .= '<td align="center">' . $m->time . '</td>'; }
	else { $return .= '<td align="center"> - </td>'; }
	if ($m->t_cluster) {
		$tc = DB::cluster(code: $m->t_cluster);
		$return .= '<td align="center"><a href="' . $base_url . '/' . $uni . '/' . $m->t_cluster . '">' . $tc->name . '<a></td>'; 
	} else { $return .= '<td align="center"> - </td>'; }	
	if ($m->t_sector) { $return .= '<td align="center"><a href="' . $base_url . '/' . $uni . '/' . $m->t_sector . '">' . $m->t_sector . '<a></td>'; }
	else { $return .= '<td align="center"> - </td>'; }
	if ($m->t_loc) { $return .= '<td align="center"><a href="' . $base_url . '/' . $uni . '/' . $m->t_sector . '/' . $m->t_x . '/' . $m->t_y . '">' . $m->t_loc . '<br>[' . $m->t_x . ',' . $m->t_y . ']</a></td>'; }
	else { 
		if ($m->t_x) { $return .= '<td align="center"><a href="' . $base_url . '/' . $uni . '/' . $m->t_sector . '/' . $m->t_x . '/' . $m->t_y . '">[' . $m->t_x . ',' . $m->t_y . ']</a></td>'; }
		else { $return .= '<td align="center"> - </td>';  }
	}
	if ($m->war) { 
		$return .= '<td align="center" class="war">' . $m->war . '</td>'; 
	}
	else if ($m->credits) { $return .= '<td align="center">' . $m->credits . '</td>'; }
	else { $return .= '<td align="center"> - </td>'; }
	$return .= '<td align="center">' . $m->tick . '</td>';
	$return .= '</tr>';
} } else {
	$return .= '<tr><td colspan="12" align="center"><h1>No Mission Data Available</h1></td></tr>';
}

$return .= '</table>';

$db->close();

echo $return;
