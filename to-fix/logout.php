<?php
declare(strict_types=1);
require_once('app/settings.php');

use Pardusmapper\Core\MySqlDB;

$db = new MySqlDB;

// Set Univers Variable and Session Name
if (!isset($_REQUEST['uni'])) { include('landing.php'); exit; }

session_name($uni = $db->protect($_REQUEST['uni']));

session_start();

$db->query('UPDATE ' . $uni . '_Users SET logout = UTC_TIMESTAMP() WHERE username = \'' . $name . '\'');
$db->close();

session_destroy();
session_write_close();
$url = $base_url . '/' . $uni . '/index.php';
header("Location: $url");
