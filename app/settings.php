<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));
date_default_timezone_set("UTC");

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Pardusmapper\Core\Environment;
use Pardusmapper\Core\Settings;

Environment::load();
Settings::init();

$testing = Settings::$TESTING;
$debug = Settings::$DEBUG;

$base_url = Settings::$BASE_URL;
$img_url = Settings::$IMG_DIR;
// Override the $img_url if an image pack is used 
if (isset($_COOKIE['imagepack'])) {
	$img_url = rtrim($_COOKIE['imagepack'], '/') . '/';
}

if ($testing) { $base_url .= '/TestMap'; }

$css = $base_url . '/resources/main.css';
$game_css = $base_url . '/resources/game.css';
$cluster_css = $base_url . '/resources/cluster.css';
$index_css = $base_url . '/resources/index.css';
$r_css = $base_url. '/resources/resources.css';
$n_css = $base_url. '/resources/npc.css';
$m_css = $base_url. '/resources/mission.css';

if ($testing || $debug) { 
	error_reporting(E_STRICT | E_ALL | E_NOTICE);
}

if (Settings::$SHOW_EXCEPTIONS) {
    ini_set('display_errors', '1');
	error_reporting(E_STRICT | E_ALL | E_NOTICE);
    set_exception_handler('mapper_exception_handler');
}
