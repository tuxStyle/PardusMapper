<?php

declare(strict_types=1);
require_once('app/settings.php');

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;
use Pardusmapper\DB;
use Pardusmapper\Request;
use Pardusmapper\Session;

$db = MySqlDB::instance();  // Create an instance of the Database class

// Set Univers Variable and Session Name
$uni = Request::uni();
$sector = Request::pstring(key: 'sector');
http_response(is_null($uni), ApiResponse::OK, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));
http_response(is_null($sector), ApiResponse::OK, 'sector query parameter is required');

session_name($uni);
session_start();

$security = Session::pint(key: 'security', default: 0);
$user = Session::pstring(key: 'user');
$id = Session::pint(key: 'id');


$r_list = []; // Initialize $r_list
$res_list = []; // Initialize $res_list

$cluster = DB::cluster(sector: $sector)->code;

// Make list of all resources in sector
$db->execute(sprintf('SELECT * FROM %s_Buildings WHERE sector = ?', $uni), [
    's', $sector
]);

while ($r_single = $db->nextObject()) {
    $r_list[] = $r_single->image;
}

$r_list = array_unique($r_list);
foreach ($r_list as $r_single) {
    $u = DB::upkeep_static(fg: $r_single, upkeep: 1);
    while ($u = $db->nextObject()) {
        $res_list[] = $u->res;
    }
}

sort($res_list); // Now $res_list will not be null
$res_list = array_unique($res_list);
array_unshift($res_list, 'All');

$db->close();

require_once(templates('resources'));
