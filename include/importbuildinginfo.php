<?php
declare(strict_types=1);
require_once('../app/settings.php');

/** @var string $base_url */

use Pardusmapper\CORS;

CORS::pardus();


$site = $base_url . '/include/';

if (isset($_REQUEST['bi'])) {
	$site .= 'importbuildingindex.php?';
}
if (isset($_REQUEST['building']) || isset($_REQUEST['bt'])) {
	$site .= 'importothersbuildinginfo.php?';
}
if (isset($_REQUEST['bts']) || isset($_REQUEST['level']) || isset($_REQUEST['bm'])) {
	$site .= 'importownbuildinginfo.php?';
}
if (isset($_REQUEST['planet']) || isset($_REQUEST['pt'])) {
	$site .= 'importplanetinfo.php?';
}
if (isset($_REQUEST['sb']) || isset($_REQUEST['sbt']) || isset($_REQUEST['squads']) || isset($_REQUEST['sbb'])) {
	$site .= 'importstarbaseinfo.php?';
}
$site .= $_SERVER['QUERY_STRING'];
header("Location: $site");
