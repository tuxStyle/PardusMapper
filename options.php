<?php
declare(strict_types=1);
require_once('app/settings.php');

use Pardusmapper\Core\MySqlDB;
use Pardusmapper\DB;
use Pardusmapper\Request;

$db = new MySqlDB;

// Set Univers Variable and Session Name
$uni = Request::uni();
if (is_null($uni)) { require_once(templates('lannding')); exit; }

session_name($uni);
session_start();

$url = Request::url();
if (is_null($url)) { $url = $_SERVER['HTTP_REFERER']; }

$image = Request::img(key: 'image');
$loc2 = Request::loc(key: 'loc2');
$loc3 = Request::loc(key: 'loc3');

if ($debug) print_r($_SESSION);
if ($debug) echo '<br>';

$logged_in = 0;
if (isset($_SESSION['user'])) { $logged_in = 1; }

if (!is_null($image)) {
	if ($logged_in) {
		$db->execute(sprintf('UPDATE %s_Users SET imagepack = ? WHERE username = ?', $uni), [
            'ss', ($loc2 . $loc3), $_SESSION['user']
        ]);
	}
	setcookie("imagepack",$loc2 . $loc3,time()+60*60*24*365,"/");
}

$oldpwd = Request::pstring(key: 'oldpwd');
$newpwd1 = Request::pstring(key: 'newpwd1');
$newpwd2 = Request::pstring(key: 'newpwd2');

$invalidoldpwd = 0;

if (is_null($oldpwd)) {
	if ($debug) echo 'Old Password was not entered<br>';
	unset($_REQUEST['change']);
} else {
	if ($debug) echo 'Old Password entered<br>';
    $u = DB::user($_SESSION['user'], $uni);
	if (!is_null($u)) {
		if ($u->password != sha1($oldpwd)) {
			if ($debug) echo 'Old Password did not match<br>';
			$invalidoldpwd = 1;
			unset($_REQUEST['change']);
		}
	}
}

$invalidpwd = 0;
$newpwdnotentered = 0;
if (!is_null($newpwd1)){
	if ($newpwd1 !== $newpwd2) {
		if ($debug) echo 'New Passwords do not Match<br>';
		$invalidpwd = 1;
		unset($_REQUEST['change']);
	} else {
		if ($debug) echo 'New Passwords Match<br>';
		$newpwd = $newpwd1;
	}
} else {
	if ($debug) echo 'New Password not set<br>';
	$newpwdnotentered = 1;
	unset($_REQUEST['change']);
}


if (isset($_REQUEST['change'])) {
	$db->execute(sprintf('UPDATE %s_Users SET password = ? WHERE username = ?', $uni), [
        'ss', sha1($newpwd), $_SESSION['user'],
    ]);
	$db->close();
	header("Location: {$url}");
} else {

    if ($debug) echo 'Change not Set<br>';
    $db->close();

    require_once(templates('options'));
}