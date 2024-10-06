<?php
declare(strict_types=1);

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;
use Pardusmapper\CORS;
use Pardusmapper\DB;
use Pardusmapper\Request;

require_once('../app/settings.php');

CORS::pardus();

debug($_REQUEST);

// Set Univers Variable
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::BADREQUEST, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

// Get Version
$minVersion = 5.8;
$version = Request::pfloat(key: 'version', default: 0);

http_response($version < $minVersion, ApiResponse::BADREQUEST, sprintf('version query parameter is required or invalid: %s ... minumum version: %s', ($uni ?? 'null'), $minVersion));

$loc = Request::pint(key: 'loc');
http_response(is_null($loc), ApiResponse::BADREQUEST, sprintf('location(loc) query parameter is required or invalid: %s', $loc ?? 'null'));

// Delete All Crew for This Location
DB::crew_delete(universe: $uni, location: $loc);

$crew = Request::pstring(key: 'crew');
http_response(is_null($crew), ApiResponse::OK, sprintf('for the current location, no crew was found: %s', $crew ?? 'null'));


// Get Sector And Cluster Info
$s = DB::sector(id:$loc);
$c = DB::cluster(id: $s->c_id);

$data = explode('~', (string) $crew);
for ($i = 1; $i < sizeof($data); $i++) {
	debug($data[$i]);

	$temp = explode(',', $data[$i]);
    debug($temp);

	// Insert New Crew into DB
    DB::crew_create(name: $temp[0], location: $loc, universe: $uni);

    $params = [];

    // Cluster and Sector
    $params['cluster'] = $c->code;
    $params['sector'] = $s->name;

    // Update Image
    $params['image'] = $temp[1];

	// Update Type
    $params['type'] = $temp[2];

	if ($temp[2] === "Legendary Crew Member") {
		// Update Title
        $params['title'] = $temp[3];

		// Update 2nd Job
        $params['job2'] = $temp[5];
	} else {
		// Update Level
        $params['level'] = (int)$temp[6];
	}

	// Update 1st Job
    $params['job1'] = $temp[4];

	// Update Fee
    $params['fee'] = (int)$temp[7];

	// Update pay
    $params['pay'] = (int)$temp[8];

	// Update Date
	DB::crew_update(name: $temp[0], params: $params, universe: $uni);
}
