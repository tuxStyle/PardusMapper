<?php 
declare(strict_types=1);
require_once('../app/settings.php');

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\CORS;
use Pardusmapper\Request;

CORS::mapper();

// Set Univers Variable and Session Name
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));


session_name($uni);
session_start();

require_once(templates('news'));
