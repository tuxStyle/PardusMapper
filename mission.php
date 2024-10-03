<?php
declare(strict_types=1);
require_once('app/settings.php');

use Pardusmapper\Coordinates;
use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Request;
use Pardusmapper\Session;
use Pardusmapper\DB;
use Pardusmapper\Core\Settings;

// Universe is required
$uni = Request::uni();
$sector = Request::sector();
$cluster = Request::cluster();

http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

// Set Univers Variable and Session Name
session_name($uni);
session_start();

if($debug) xp(__FILE__, $_SESSION);

$security = Session::security();
$rank = Session::rank();
$comp = Session::comp();
$faction = Session::faction();
$syndicate = Session::syndicate();

$x1 = Request::x(key: 'x1');
$y1 = Request::y(key: 'y1');

$s = null;
$c = null;
if (!is_null($sector)) {
    $s = DB::sector(sector: $sector);
    $c = DB::cluster(id: $s->c_id);
    $cluster = $c->code;
}

$id = null;
if (!is_null($x1) && !is_null($y1)) {
	$id = Coordinates::getID($s->s_id,$s->rows,$x1,$y1);
}

$today = date("Y-m-d");
$mission_list = Settings::MISSIONS_LIST;
sort($mission_list);

if (isset($_SESSION['loaded']) && ((strtotime($today) - strtotime($_SESSION['loaded'])) < 172800)) {
    require_once(templates('mission'));
} 
else { 
    require_once(templates('notlogged'));
}