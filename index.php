<?php
declare(strict_types=1);
require_once('app/settings.php');

use Pardusmapper\Request;

$uni = Request::uni();
if (is_null($uni)) { require_once(templates('landing')); exit; }

// Set Univers Variable and Session Name
session_name($uni);
session_start();

$cluster = Request::pstring(key: 'cluster');
$gems = Request::pstring(key: 'gems');

require_once(templates('map'));
