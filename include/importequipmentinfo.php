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

$tab = Request::pstring(key: 'tab');
http_response(is_null($tab), ApiResponse::BADREQUEST, sprintf('equipment(tab) query parameter is required or invalid: %s', $tab ?? 'null'));

$eq = Request::pstring(key: 'eq');
http_response(is_null($eq), ApiResponse::OK, sprintf('equipment(eq) query parameter is required or invalid: %s', $eq ?? 'null'));

// Get Sector And Cluster Info
$s = DB::sector(id:$loc);
$c = DB::cluster(id: $s->c_id);

$data = explode('~', $eq);
switch ($tab) {
	case 'weapon':
	case 'drive':
	case 'armor':
	case 'shield':
	case 'special':
		for ($i = 1; $i < sizeof($data); $i++) {
			debug($data[$i]);

			$temp = explode(',', $data[$i]);
            debug($temp);

			//Verify Item is not already in the DB
            $e = DB::equipment(name: $temp[1], location: $loc, universe: $uni);
			if (!$e) {
                DB::equipment_create(name: $temp[1], location: $loc, universe: $uni);
			}

            $params = [];

			// Update Cluster and Sector
            $params['cluster'] = $c->code;
            $params['sector'] = $s->name;

            // Update Image
            $params['image'] = $temp[0];

            // Update Price
            $params['price'] = (int)$temp[2];

            // Update Amount
            $params['amount'] = (int)$temp[3];

            // Update Type
            $params['type'] = $tab;

            DB::equipment_update(name: $temp[1], location: $loc, params: $params, universe: $uni);
		}
		break;
}

DB::building_equipment_update(id: $loc, params: [], universe: $uni);
