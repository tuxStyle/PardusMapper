<?php

require_once('../include/mysqldb.php');
$db = new mysqldb;

$uni = $db->protect($_POST['uni']);
session_name($uni);

session_start();

$security = 0;
if (isset($_SESSION['security'])) { $security = $db->protect($_SESSION['security']); }

if (isset($_COOKIE['imagepack'])) {
	$img_url = $_COOKIE['imagepack'];
	if ($img_url[count($img_url) - 1] != '/')	{$img_url .= '/'; }
}

$db->query('SELECT * from Pardus_Sectors order by name');
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
?>