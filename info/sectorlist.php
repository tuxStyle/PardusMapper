<?php 
declare(strict_types=1);
require_once('../app/settings.php');

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;
use Pardusmapper\CORS;
use Pardusmapper\Post;
use Pardusmapper\Session;

CORS::mapper();

$db = new MySqlDB();

$uni = Post::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

session_name($uni);
session_start();

$security = Session::security();

$sector = [];
$db->execute('SELECT * from Pardus_Sectors order by name');
while ($s = $db->nextObject()) { $sector[] = $s; }
$db->close();

//$return = "<table style=\"height:100px; overflow-y:auto\">";
$return = '<table>';
$return .= '<tr>';
$return .= '<th>Sector</th>';
$return .= '<th>Cluster</th>';
$return .= '</tr>';

$i = 0;
foreach ($sector as $key => $s) {
	//$c = $s[0];
	//$g = $s[1];
				
	if ($i++ % 2 == 0) {
		$return .= '<tr class="alternating">';
	} else {
		$return .= '<tr>';
	}
	$return .= '<td align="center">';
		//$return .= $s->name;
		$return .= '<a href="' . $base_url . '/' . $uni . '/' . $s->name . '">' . $s->name . '</a>';
	$return .= '</td>';
	$return .= '<td align="center">';
		//$return .= $s->c_name;
		$return .= '<a href="' . $base_url . '/' . $uni . '/' . $s->c_name . '">'. $s->c_name . '</a>';
	$return .= '</td>';

	$return .= '</tr>';
}
$return .= '</table>';
echo $return;
