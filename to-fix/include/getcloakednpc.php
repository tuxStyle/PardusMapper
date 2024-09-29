<?php
/*
if($_SERVER['HTTP_ORIGIN'] == "https://orion.pardus.at")  {  header('Access-Control-Allow-Origin: https://orion.pardus.at'); }
if($_SERVER['HTTP_ORIGIN'] == "https://artemis.pardus.at")  {  header('Access-Control-Allow-Origin: https://artemis.pardus.at'); }
if($_SERVER['HTTP_ORIGIN'] == "https://pegasus.pardus.at")  {  header('Access-Control-Allow-Origin: https://pegasus.pardus.at'); }
*/
header('Access-Control-Allow-Origin: https://*.pardus.at');

require_once('mysqldb.php');
$db = new mysqldb();

$uni = $db->protect($_REQUEST['uni']);
$data = explode("~", $db->protect($_REQUEST['data']));

$return = '';
foreach ($data as $i => $loc) {
	if ($i === 0) {
		continue; // Skip the first element
	}
	
	$db->query('SELECT * FROM ' . $uni . '_Maps WHERE id = ' . $loc);
	$m = $db->nextObject();

	if ($m->npc_cloaked) {
		$return .= '~' . $loc . ',' . $m->npc;
	}
}

$db->close();
echo $return;
