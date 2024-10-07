<?php 
declare(strict_types=1);
require_once('../app/settings.php');

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;
use Pardusmapper\CORS;
use Pardusmapper\Post;
use Pardusmapper\Session;

CORS::mapper();

$db = MySqlDB::instance();

// Set Univers Variable and Session Name
$uni = Post::uni();
http_response(is_null($uni), ApiResponse::OK, sprintf('uni query parameter is required or invalid: %s', $uni ?? 'null'));

session_name($uni);
session_start();

$sessId = Session::pint(key: 'id');
$id = Post::pint(key: 'id');
$loc = Post::pint(key: 'loc');


if ($id !== $sessId) { return; }

if (isset($_POST['add'])) { 
    $db->execute(sprintf('INSERT INTO %s_Personal_Resources (id,loc) VALUES (?, ?)', $uni), [
        'ii', $id, $loc
    ]); 
}
else {
    $db->execute(sprintf('DELETE FROM %s_Personal_Resources WHERE id = ? AND loc = ?', $uni), [
        'ii', $id, $loc
    ]);
}
