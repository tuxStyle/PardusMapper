<?php
declare(strict_types=1);

use Pardusmapper\Coordinates;
use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;
use Pardusmapper\CORS;
use Pardusmapper\DB;
use Pardusmapper\Request;

require_once('../app/settings.php');

CORS::mapper();

debug($_REQUEST);

// Set Univers Variable
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

session_name($uni);
session_start();

// Get Location
$id = Request::pint(key: 'id');

$m = DB::map(id: $id, universe: $uni);
if (!is_null($m->fg)||!is_null($m->wormhole)) {
	debug('Removing WH');
	$db->removeWH($uni,$id);
	echo "<script>window.close();</script>";
}

$db->close();
