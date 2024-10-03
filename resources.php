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
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));
http_response(is_null($sector), ApiResponse::BADREQUEST, 'sector query parameter is required');

session_name($uni);
session_start();

$security = Session::security();
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
    $r_list[] = $r_single->name;
}

$r_list = array_unique($r_list);
foreach ($r_list as $r_single) {
    $db->execute('SELECT * FROM Pardus_Upkeep_Data WHERE name = ? AND upkeep = 1', [
        's', $r_single
    ]);

    while ($u = $db->nextObject()) {
        $res_list[] = $u->res;
    }
}

sort($res_list); // Now $res_list will not be null
$res_list = array_unique($res_list);
array_unshift($res_list, 'All');

$db->close();

require_once(templates('resources'));
