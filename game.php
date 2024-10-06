<?php
declare(strict_types=1);
require_once('app/settings.php');

header("Cache-Control: private, max-age=604800");
header("Expires: ".gmdate('r', time()+604800));

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Request;
use Pardusmapper\DB;
use Pardusmapper\Session;

$cluster = null;
$code = null; // Handle case where no cluster was found
$id = null;
$shownpc = 0;

$uni = Request::uni();
$security = Session::pint(key: 'security', default: 0);
$sector = Request::pstring(key: 'sector');
$x2 = Request::pint(key: 'x2');
$y2 = Request::pint(key: 'y2');

// Universe and Sector name are required
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));
http_response(is_null($sector), ApiResponse::BADREQUEST, 'sector query parameter is required');

// Set Univers Variable and Session Name
session_name($uni);
session_start();

// Load Cluster and Sector objects
if (!is_null($sector)) {
    $s = DB::sector(sector: $sector); // Fetch the sector object
    http_response(is_null($s), ApiResponse::BADREQUEST, sprintf('sector not found: %s', $sector)); // exit if not found in DB

    $cluster = DB::cluster(sector: $sector); // Fetch the cluster object
    http_response(is_null($cluster), ApiResponse::BADREQUEST, sprintf('cluster not found for sector: %s', $sector)); // exit if not found in DB

    $code = $cluster->code;
}

if (!is_null($x2) && !is_null($y2)) {
	$id = $s->s_id + ($x2 * $s->rows) + $y2;
}

$title = $sector . ' Sector Map';

require_once(templates('sectormap'));
