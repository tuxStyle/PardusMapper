<?php

use Pardusmapper\Core\Settings;

header('Access-Control-Allow-Origin: ' . Settings::$BASE_URL);

require_once("mysqldb.php");
$db = new mysqldb;
$debug = true;

if ($debug) print_r($_REQUEST);
if ($debug) echo '<br>';

// Set Univers Variable and Session Name
if (!isset($_REQUEST['uni'])) { exit; }

$uni = $db->protect($_REQUEST['uni']);

// Get Location
$id = 0;
if (isset($_REQUEST['id'])) { $id = $db->protect($_REQUEST['id']); }


$db->query('SELECT * FROM `' . $uni . '_Maps` where id = ' . $id);
$m = $db->nextObject();
if (!is_null($m->fg)||!is_null($m->wormhole)) {
	if ($debug) echo 'Removing WH<br>';
	$db->removeWH($uni,$id);
	echo "<script>window.close();</script>";
}

$db->close();
?>