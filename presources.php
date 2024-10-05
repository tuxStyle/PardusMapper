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

debug($_SESSION);

$security = Session::security();
$user = Session::pstring(key: 'user');
$id = Session::pint(key: 'id');
$pilot = Request::pstring(key: 'pilot');

$s = DB::sector(sector:$sector);
$c = DB::cluster(id: $s->c_id);
$cluster = $c->code;

if (!(isset($pilot) && $pilot === $user)) {
	$url = $base_url . '/' . $uni . '/' . $sector . '/resources';
	header("Location: $url");
}

$r_list = [];
$res_list = [];

if (isset($id) && $id > 0) {
	$db->execute(sprintf('SELECT * FROM %s_Buildings WHERE id IN (SELECT loc FROM %s_Personal_Resources WHERE id = ?)', $uni, $uni), [
        'i', $id
    ]);
	while ($r_single = $db->nextObject()) { $r_list[] = $r_single->name; }
	$r_list = array_unique($r_list);
	foreach ($r_list as $r_single) {
        $u = DB::upkeep_static(name: $r_single, upkeep: 1);
        while ($u = $db->nextObject()) { $res_list[] = $u->res; }
	}
	if ($res_list) { 
		sort($res_list);
		$res_list = array_unique($res_list);
		array_unshift($res_list,'All');
	} else {
		$res_list[] = 'All';
	}
}
$db->close();

require_once(templates('presources'));
