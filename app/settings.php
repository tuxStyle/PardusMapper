<?php
declare(strict_types=1);

date_default_timezone_set("UTC");

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Pardusmapper\Core\Environment;
use Pardusmapper\Core\Settings;

Environment::load();
Settings::init();

$base_url = Settings::$BASE_URL;
$testing = Settings::$TESTING;
$debug = Settings::$DEBUG;
$img_url = Settings::$IMG_DIR;

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

function upkeep($base, $level): float
{
    return round($base * (1 + .4 * ($level - 1)));
}

function production($base, $level): float
{
    return round($base * (1 + .5 * ($level - 1)));
}

function compareLoc($x, $y): int
{
    // Compare X
    if ($x->x == $y->x) {
        // Compare Y
        if ($x->y == $y->y) {
            return 0;
        } elseif ($x->y < $y->y) {
            return -1;
        } else {
            return 1;
        }
    } elseif ($x->x < $y->x) {
        return -1;
    } else {
        return 1;
    }
}

function compareLocRev($x, $y): int
{
    // Compare X
    if ($x->x == $y->x) {
        // Compare Y
        if ($x->y == $y->y) {
            return 0;
        } elseif ($x->y < $y->y) {
            return 1;
        } else {
            return -1;
        }
    } elseif ($x->x < $y->x) {
        return 1;
    } else {
        return -1;
    }
}

function compareName($x, $y): int
{
    if ($x->name == $y->name) {
        return 0;
    } elseif ($x->name < $y->name) {
        return -1;
    } else {
        return 1;
    }
}

function compareNameRev($x, $y): int
{
    if ($x->name == $y->name) {
        return 0;
    } elseif ($x->name < $y->name) {
        return 1;
    } else {
        return -1;
    }
}

function compareOwner($x, $y): int
{
    if ($x->owner == $y->owner) {
        return 0;
    } elseif ($x->owner < $y->owner) {
        return -1;
    } else {
        return 1;
    }
}

function compareOwnerRev($x, $y): int
{
    if ($x->owner == $y->owner) {
        return 0;
    } elseif ($x->owner < $y->owner) {
        return 1;
    } else {
        return -1;
    }
}

function compareAlliance($x, $y): int
{
    if ($x->alliance == $y->alliance) {
        return 0;
    } elseif ($x->alliance < $y->alliance) {
        return -1;
    } else {
        return 1;
    }
}

function compareAllianceRev($x, $y): int
{
    if ($x->alliance == $y->alliance) {
        return 0;
    } elseif ($x->alliance < $y->alliance) {
        return 1;
    } else {
        return -1;
    }
}

function compareStock($x, $y): int
{
    if ($x->stock == $y->stock) {
        return 0;
    } elseif ($x->stock < $y->stock) {
        return -1;
    } else {
        return 1;
    }
}

function compareStockRev($x, $y): int
{
    if ($x->stock == $y->stock) {
        return 0;
    } elseif ($x->stock < $y->stock) {
        return 1;
    } else {
        return -1;
    }
}

function compareTick($x, $y): int
{
    if ($x->tick == $y->tick) {
        return 0;
    } elseif ($x->tick < $y->tick) {
        return -1;
    } else {
        return 1;
    }
}

function compareTickRev($x, $y): int
{
    if ($x->tick == $y->tick) {
        return 0;
    } elseif ($x->tick < $y->tick) {
        return 1;
    } else {
        return -1;
    }
}

function compareSpotted($x, $y): int
{
    if ($x->spotted == $y->spotted) {
        return 0;
    } elseif ($x->spotted < $y->spotted) {
        return -1;
    } else {
        return 1;
    }
}

function compareSpottedRev($x, $y): int
{
    if ($x->spotted == $y->spotted) {
        return 0;
    } elseif ($x->spotted < $y->spotted) {
        return 1;
    } else {
        return -1;
    }
}

function compareUpdated($x, $y): int
{
    if ($x->updated == $y->updated) {
        return 0;
    } elseif ($x->updated < $y->updated) {
        return -1;
    } else {
        return 1;
    }
}

function compareUpdatedRev($x, $y): int
{
    if ($x->updated == $y->updated) {
        return 0;
    } elseif ($x->updated < $y->updated) {
        return 1;
    } else {
        return -1;
    }
}

function compareCluster($x, $y): int
{
    if ($x->cluster == $y->cluster) {
        return 0;
    } elseif ($x->cluster < $y->cluster) {
        return -1;
    } else {
        return 1;
    }
}

function compareClusterRev($x, $y): int
{
    if ($x->cluster == $y->cluster) {
        return 0;
    } elseif ($x->cluster < $y->cluster) {
        return 1;
    } else {
        return -1;
    }
}

function compareAge($x, $y): int
{
    if ($x->age == $y->age) {
        return 0;
    } elseif ($x->age < $y->age) {
        return -1;
    } else {
        return 1;
    }
}

function compareAgeRev($x, $y): int
{
    if ($x->age == $y->age) {
        return 0;
    } elseif ($x->age < $y->age) {
        return 1;
    } else {
        return -1;
    }
}

function compareType($x, $y): int
{
    if ($x->type_img == $y->type_img) {
        return 0;
    } elseif ($x->type_img < $y->type_img) {
        return -1;
    } else {
        return 1;
    }
}

function compareTypeRev($x, $y): int
{
    if ($x->type_img == $y->type_img) {
        return 0;
    } elseif ($x->type_img < $y->type_img) {
        return 1;
    } else {
        return -1;
    }
}

function compareAmount($x, $y): int
{
    if ($x->amount == $y->amount) {
        return 0;
    } elseif ($x->amount < $y->amount) {
        return -1;
    } else {
        return 1;
    }
}

function compareAmountRev($x, $y): int
{
    if ($x->amount == $y->amount) {
        return 0;
    } elseif ($x->amount < $y->amount) {
        return 1;
    } else {
        return -1;
    }
}

function compareTime($x, $y): int
{
    if ($x->time == $y->time) {
        return 0;
    } elseif ($x->time < $y->time) {
        return -1;
    } else {
        return 1;
    }
}

function compareTimeRev($x, $y): int
{
    if ($x->time == $y->time) {
        return 0;
    } elseif ($x->time < $y->time) {
        return 1;
    } else {
        return -1;
    }
}

function compareTobject($x, $y): int
{
    if ($x->t_loc == $y->t_loc) {
        return 0;
    } elseif ($x->t_loc < $y->t_loc) {
        return -1;
    } else {
        return 1;
    }
}

function compareTobjectRev($x, $y): int
{
    if ($x->t_loc == $y->t_loc) {
        return 0;
    } elseif ($x->t_loc < $y->t_loc) {
        return 1;
    } else {
        return -1;
    }
}

function compareTsector($x, $y): int
{
    if ($x->t_sector == $y->t_sector) {
        return 0;
    } elseif ($x->t_sector < $y->t_sector) {
        return -1;
    } else {
        return 1;
    }
}

function compareTsectorRev($x, $y): int
{
    if ($x->t_sector == $y->t_sector) {
        return 0;
    } elseif ($x->t_sector < $y->t_sector) {
        return 1;
    } else {
        return -1;
    }
}

function compareTloc($x, $y): int
{
    // Compare X
    if ($x->t_x == $y->t_x) {
        // Compare Y
        if ($x->t_y == $y->t_y) {
            return 0;
        } elseif ($x->t_y < $y->t_y) {
            return -1;
        } else {
            return 1;
        }
    } elseif ($x->t_x < $y->t_x) {
        return -1;
    } else {
        return 1;
    }
}

function compareTlocRev($x, $y): int
{
    // Compare X
    if ($x->t_x == $y->t_x) {
        // Compare Y
        if ($x->t_y == $y->t_y) {
            return 0;
        } elseif ($x->t_y < $y->t_y) {
            return 1;
        } else {
            return -1;
        }
    } elseif ($x->t_x < $y->t_x) {
        return 1;
    } else {
        return -1;
    }
}

function compareReward($x, $y): int
{
    if ($x->credits == $y->credits) {
        return 0;
    } elseif ($x->credits < $y->credits) {
        return -1;
    } else {
        return 1;
    }
}

function compareRewardRev($x, $y): int
{
    if ($x->credits == $y->credits) {
        return 0;
    } elseif ($x->credits < $y->credits) {
        return 1;
    } else {
        return -1;
    }
}
