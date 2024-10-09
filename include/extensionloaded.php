<?php
declare(strict_types=1);
define('REQUEST_SOURCE', 'pardus');

use Pardusmapper\Core\MySqlDB;
use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Request;
use Pardusmapper\DB;
use Pardusmapper\CORS;

require_once('../app/settings.php');

CORS::pardus();

$db = MySqlDB::instance(); // Create an instance of the Database class

debug($_REQUEST);

// Set Univers Variable and Session Name
$uni = Request::uni();
http_response(is_null($uni), ApiResponse::OK, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));
// TODO: i think it needs session start here

$id = Request::pint(key: 'id', default: 0);
$user = Request::pstring(key: 'user', default: 'Unknown');
$version = Request::pstring(key: 'version', default: '0.0');
$browser = Request::pstring(key: 'browser', default: 'Unknown');
$faction = Request::pstring(key: 'faction', default: 'Unknown');
$syndicate = Request::pstring(key: 'syndicate', default: 'Unknown');
$comp = Request::pint(key: 'comp');
$rank = Request::pint(key: 'rank');
$ip = $_SERVER['REMOTE_ADDR'];

if (isset($_REQUEST['el'])) {
    if ($id) {
        debug('Looking up user by ' . $id);

        $u = DB::user(id: $id, universe: $uni);

        if ($u) {
            debug($id . ' For ' . $user . ' (1) Already in DB Updating');
            debug($u);

            $db->execute(sprintf('UPDATE %s_Users SET loaded = UTC_TIMESTAMP(), version = ?, browser = ?, username = ?, ip = ? WHERE id = ?', $uni), [
                'dsssi', $version, $browser, $user, $ip, $id
            ]);
        } else {
            debug($id . ' For ' . $user . ' Not in DB Trying Name');

            $u = DB::user(username: $user, universe: $uni);

            if ($u) {
                debug($user . ' (2) Already in DB Updating');
                debug($u);

                $db->execute(sprintf('UPDATE %s_Users SET loaded = UTC_TIMESTAMP(), version = ?, browser = ?, id = ?, ip = ? WHERE username = ?', $uni), [
                    'dsiss', $version, $browser, $id, $ip, $user
                ]);
            } else {
                debug('New user Inserting ' . $user);

                $db->execute(sprintf('INSERT INTO %s_Users (user_id, id, username, password, security, loaded, version, browser, ip) VALUES (NULL, ?, ?, ?, 0, UTC_TIMESTAMP(), ?, ?, ?)', $uni), [
                    'issdss', $id, $user, sha1("n0p2ssword"), $version, $browser, $ip
                ]);
                
                $db->execute(sprintf('SELECT * FROM %s_Users WHERE username = ?', $uni), [
                    's', $user
                ]);
                
                if ($u = $db->nextObject()) {
                    debug($user . ' Added To DB');
                    debug($u);
                }
            }
        }
    } else {
        debug($user . ' Trying User Name');

        $db->execute(sprintf('SELECT * FROM %s_Users WHERE username = ?', $uni), [
            's', $user
        ]);

        if ($u = $db->nextObject()) {
            debug($user . ' Already in DB Updating');
            debug($u);

            $db->execute(sprintf('UPDATE %s_Users SET loaded = UTC_TIMESTAMP(), version = ?, browser = ?, ip = ? WHERE username = ?', $uni), [
                'dsss', $version, $browser, $ip, $user
            ]);
        } else {
            debug('New user Inserting ' . $user);

            $db->execute(sprintf('INSERT INTO %s_Users (user_id, username, password, security, loaded, version, browser, ip) VALUES (NULL, ?, ?, 0, UTC_TIMESTAMP(), ?, ?, ?)', $uni), [
                'ssidss', $user, sha1("n0p2ssword"), $version, $browser, $ip
            ]);
            
            $db->execute(sprintf('SELECT * FROM %s_Users WHERE username = ?', $uni), [
                's', $user
            ]);

            if ($u = $db->nextObject()) {
                debug($user . ' Added To DB');
                debug($u);
            }
        }
    }
}
if (isset($_REQUEST['lud'])) {
    $db->execute(sprintf('SELECT * FROM %s_Users WHERE username = ?', $uni), [
        's', $user
    ]);
    if ($u = $db->nextObject()) {
        debug($user . ' Already in DB Updating');
        debug($u);

        if (is_null($faction) || $faction == 'Unknown') {
            debug(sprintf('User: %s no faction, set to null', $user));

            $db->execute(sprintf('UPDATE %s_Users SET loaded = UTC_TIMESTAMP(), faction = NULL WHERE username = ?', $uni), [
                's', $user
            ]);
        } else {
            debug(sprintf('User: %s save faction: %s', $user, $faction));

            $db->execute(sprintf('UPDATE %s_Users SET loaded = UTC_TIMESTAMP(), faction = ? WHERE username = ?', $uni), [
                'ss', $faction, $user
            ]);
        }

        if (is_null($syndicate) || $syndicate == 'Unknown') {
            debug(sprintf('User: %s no syndicate, set to null', $user));

            $db->execute(sprintf('UPDATE %s_Users SET loaded = UTC_TIMESTAMP(), syndicate = NULL WHERE username = ?', $uni), [
                's', $user
            ]);
        } else {
            debug(sprintf('User: %s save syndicate: %s', $user, $syndicate));

            $db->execute(sprintf('UPDATE %s_Users SET loaded = UTC_TIMESTAMP(), syndicate = ? WHERE username = ?', $uni), [
                'ss', $syndicate, $user
            ]);
        }
        if (is_null($rank)) {
            debug(sprintf('User: %s no rank, set to null, save competency', $user));

            $db->execute(sprintf('UPDATE %s_Users SET loaded = UTC_TIMESTAMP(), comp = ?, rank = NULL WHERE username = ?', $uni), [
                'is', $comp, $user
            ]);
        } else {
            debug(sprintf('User: %s save competency: %s and rank: %s', $user, $comp, $rank));

            $db->execute(sprintf('UPDATE %s_Users SET loaded = UTC_TIMESTAMP(), comp = ?, rank = ? WHERE username = ?', $uni), [
                'iis', $comp, $rank, $user
            ]);
        }

        debug(sprintf('User: %s save IP: %s', $user, $ip));

        $db->execute(sprintf("UPDATE %s_Users SET ip = ? WHERE username = ?", $uni), [
            'ss', $ip, $user
        ]);
    } else {
        debug($user . ' Not found, cannot update');
    }
}
