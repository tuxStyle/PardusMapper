<?php 
declare(strict_types=1);
require_once('../app/settings.php');

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;
use Pardusmapper\CORS;
use Pardusmapper\Post;
use Pardusmapper\Session;

CORS::mapper();

$db = MySqlDB::instance();

$uni = Post::uni();
$sector = Post::pstring(key: 'sector');
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));
http_response(is_null($sector), ApiResponse::BADREQUEST, 'sector query parameter is required');

$resource = Post::pstring(key: 'resource', default: '');
$sort = Post::pstring(key: 'sort', default: '');
$order = Post::pstring(key: 'order');
$pilot = Post::pstring(key: 'pilot');

session_name($uni);
session_start();

$security = Session::pint(key: 'security', default: 0);
$user = Session::pstring(key: 'user');
$id = Session::pint(key: 'id');

$buildings = [];
$return = '';
$sort_by = '';

if (strlen($sort)) {
	$sort_by = ' ORDER BY ';
	for($i=0;$i<strlen($sort);$i++) {
		switch(substr($sort,$i,1)) {
			case "L" :
				$sort_by .= "x ";
				if ($order & 1) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				$sort_by .= "y ";
				if ($order & 1) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "B" :
				$sort_by .= "name ";
				if ($order & 2) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "S" :
				if (strtolower($resource) != 'all') { $sort_by .= "res_stock "; }
				else { $sort_by .= "stock "; }
				if ($order & 4) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "T" :
				$sort_by .= "stock_updated ";
				if ($order & 8) { $sort_by .= " DESC, "; }
				else { $sort_by .= " ASC, "; }
				break;
			case "O" :
				$sort_by .= "owner ";
				if ($order & 16) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
			case "A" :
				$sort_by .= "alliance ";
				if ($order & 32) { $sort_by .= " ASC, "; }
				else { $sort_by .= " DESC, "; }
				break;
		}
	}
	$sort_by = substr($sort_by,0,-2);
}

if (isset($pilot) && $pilot === $user) {
	if (strtolower($resource) != 'all') {
		$sql = 'SELECT *
                    , UTC_TIMESTAMP() "today"
                    , (SELECT stock FROM %s_New_Stock s WHERE b.id = s.id AND name = ?) "res_stock"
                    , (SELECT amount FROM %w_New_Stock s WHERE b.id = s.id AND name = ?) "amount"
                    , (SELECT max FROM %s_New_Stock s WHERE b.id = s.id AND name = ?) "max"
                FROM %s_Buildings b 
                WHERE id IN (SELECT loc FROM %s_Personal_Resources WHERE id = ?)
                AND b.name IN (SELECT name from Pardus_Upkeep_Data WHERE res = ? AND upkeep = 1)';
        $query = sprintf($query, $uni, $uni, $uni, $uni, $uni);
        $params = ['sssis', $resource, $resource, $resource, $id, $resource];
	} else {
		$query = sprintf('SELECT *, UTC_TIMESTAMP() "today" FROM %s_Buildings b WHERE b.id IN (SELECT loc FROM %s_Personal_Resources WHERE id = ?)', $uni, $uni);
        $params = ['i', $id];
	}
} else {
	if (strtolower($resource) != 'all') {
		$sql = 'SELECT *
                    , UTC_TIMESTAMP() "today"
                    , (SELECT stock FROM %s_New_Stock s WHERE b.id = s.id AND name = ?) "res_stock"
                    , (SELECT amount FROM %s_New_Stock s WHERE b.id = s.id AND name = ?) "amount"
                    , (SELECT max FROM %s_New_Stock s WHERE b.id = s.id AND name = ?) "max" 
                FROM %s_Buildings b 
                WHERE sector = ?
                AND b.name IN (SELECT name from Pardus_Upkeep_Data WHERE res = ?AND upkeep = 1)';
        $query = sprintf($sql, $uni, $uni, $uni, $uni);
        $params = ['sssss', $resource, $resource, $resource, $sector, $resource];
	} else {
		$query = sprintf('SELECT *, UTC_TIMESTAMP() "today" FROM %s_Buildings b WHERE b.sector = ?', $uni);
        $params = ['s', $sector];
	}
}

if (strlen($sort_by)) { $query .= $sort_by; }

$db->execute($query, $params);


while ($q = $db->nextObject()) { 
	//Calculate Ticks Passed
	$format = '%F %T';
	$ts = strtotime($q->stock_updated);
	$date = new DateTime("@$ts");
	$date->setTime(1,25,0);
	$tick = $date->format('U');

	while ($tick < strtotime($q->stock_updated)) {
		$tick += (60 * 60 * 6);
	}

	$count = 0;
	while ($tick < strtotime($q->today)) {
		$tick += (60*60*6);
		$count++;
	}

	$q->tick = $count;

	$buildings[$q->id] = $q;
}	

$checked = [];
if (isset($id) && $id > 0) {
	$db->execute(sprintf('SELECT * FROM %s_Personal_Resources WHERE id = ?', $uni), [
        'i', $id
    ]);
	while ($a = $db->nextObject()) { $checked[] = $a->loc; }
	if ($checked) { sort($checked); }
}
$db->close();

$i = 0;

$return .= '<table id="resource_table">';
$return .= '<tr>';
if (isset($id) && $id > 0) { $return .= '<th></th>'; }
$return .= '<th>';
if (str_contains($sort,"L")) { 
	if ($order & 1) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'L\');return false;">&nbsp;Location&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'L\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'L\');return false;">Location</a>';
}
$return .= '</th>';
$return .= '<th>';
if (str_contains($sort,"B")) { 
	if ($order & 2) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'B\');return false;">&nbsp;Building&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'B\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'B\');return false;">Building</a>';
}
$return .= '</th>';
if ($security == 1 || $security == 100) {
	$return .= '<th colspan="2">';
	if (str_contains($sort,"O")) { 
		if ($order & 16) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
		$return .= '<a href="#" onClick="multiSort(\'O\');return false;">&nbsp;Owner&nbsp;</a>';
		$return .= '<a href="#" onClick="removeSort(\'O\');return false;"><span class="symbol">&times;</span></a>';
	} else {
		$return .= '<a href="#" onClick="multiSort(\'O\');return false;">Owner</a>';
	}
	$return .= '</th>';
	$return .= '<th>';
	if (str_contains($sort,"A")) { 
		if ($order & 32) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
		$return .= '<a href="#" onClick="multiSort(\'A\');return false;">&nbsp;Alliance&nbsp;</a>';
		$return .= '<a href="#" onClick="removeSort(\'A\');return false;"><span class="symbol">&times;</span></a>';
	} else {
		$return .= '<a href="#" onClick="multiSort(\'A\');return false;">Alliance</a>';
	}
	$return .= '</th>';
}
$return .= '<th>';
if (str_contains($sort,"S")) { 
	if ($order & 4) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'S\');return false;">&nbsp;Stock Level&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'S\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'S\');return false;">Stock Level</a>';
}
$return .= '</th>';
$return .= '<th>';
if (str_contains($sort,"T")) { 
	if ($order & 8) { $return .= '<span class="symbol">&uarr;</span>'; } else { $return .= '<span class="symbol">&darr;</span>'; }
	$return .= '<a href="#" onClick="multiSort(\'T\');return false;">&nbsp;Last Updated&nbsp;</a>';
	$return .= '<a href="#" onClick="removeSort(\'T\');return false;"><span class="symbol">&times;</span></a>';
} else {
	$return .= '<a href="#" onClick="multiSort(\'T\');return false;">Last Updated</a>';
}
$return .= '</th>';
$return .= '</tr>';

foreach ($buildings as $b) {
	if ($i++ % 2 == 0) {
		$return .= '<tr class="alternating">';
	} else {
		$return .= '<tr>';
	}
	if (isset($id) && $id > 0) { 
		if ($checked && in_array($b->id,$checked)) {
			$return .= '<td align="center"><input type="checkbox" id="' . $b->id . '" onClick="addInterest(this,\'' . $uni . '\',' . $id . ',' . $b->id . ');" checked></td>'; 
		} else {
			$return .= '<td align="center"><input type="checkbox" id="' . $b->id . '" onClick="addInterest(this,\'' . $uni . '\',' . $id . ',' . $b->id . ');"></td>';
		}
	}
	$return .= '<td align="center">[' . $b->x . ',' . $b->y . ']</td>';
	$return .= '<td align="center">';
	if ($b->x) {
		$return .= '<a href="#" onClick="loadDetail(\'' . $base_url . '\',\'' . $uni . '\',' . $b->id . ');return false;">' . $b->name . '</a></td>';
	} else {
		$return .= $b->name . '</td>';
	}
	if ($security == 1 || $security == 100) {
		$return .= '<td align="left">';
		if (is_string($b->faction)) {
			$return .= '<img src="' . $img_url . $b->faction . '" />';
		}
		$return .= '</td>';
		$return .= '<td align="left">' . $b->owner . '</td>';
		$return .= '<td align="center">' . $b->alliance . '</td>';
	}
	$return .= '<td align="center">';
	$return .= '<div class="bar-wrap" style="width: 100px;">';
	
	if (strtolower($resource) != 'all') { $stock_level = $b->res_stock; }
	else { $stock_level = $b->stock; }
		
	if ($stock_level == 100) {
		$return .= '<div class="bar-bar" style="width:' . $stock_level . 'px;background-color:green;"></div>';
	} elseif ($stock_level >= 75) {
		$return .= '<div class="bar-bar" style="width:' . $stock_level . 'px;background-color:blue;"></div>';
	} elseif ($stock_level >= 50) {
		$return .= '<div class="bar-bar" style="width:' . $stock_level . 'px;background-color:yellow;"></div>';
	} else {
		$return .= '<div class="bar-bar" style="width:' . $stock_level . 'px;background-color:red;"></div>';
	}
	if (isset($b->max)) {
		$amount_needed = $b->max - $b->amount;
		if ($amount_needed < 0 ) { $amount_needed = 0; }
		$return .= '<div class="bar-text" style="width: 100px;">' . $amount_needed . '</div>';
	} else {
		$return .= '<div class="bar-text" style="width: 100px;">' . $stock_level . ' %</div>';
	}
	$return .= '</div>';
	$return .= '</td>';
	$return .= '<td align="center">' . $b->tick . '</td>';
	$return .= '</tr>';
}

$return .= '</table>';
echo $return;
