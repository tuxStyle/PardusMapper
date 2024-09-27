<?php
require_once('include/mysqldb.php');
$dbClass = new mysqldb(); // Create an instance of the Database class
$db = $dbClass->getDb();  // Get the mysqli connection object

// Set Univers Variable and Session Name
if (!isset($_REQUEST['uni'])) { include('index.html'); exit; }

session_name($uni = $db->real_escape_string($_REQUEST['uni']));

session_start();

$testing = Settings::TESTING;
$debug = Settings::DEBUG;
$debug = 0;

$base_url = Settings::base_URL;
if ($testing) { $base_url .= '/TestMap'; }

$css = $base_url . '/main.css';

if (isset($_REQUEST['login'])) {
    if (!isset($_SESSION['security'])) {
        $name = null;
        $pwd = null;
        if (isset($_REQUEST['username']))  { $name = strtolower($db->real_escape_string($_REQUEST['username'])); }
        if (isset($_REQUEST['password']))  { $pwd = $db->real_escape_string($_REQUEST['password']); }

        if ($debug) { echo $name . '<br>'; }
        if ($debug) { echo sha1($pwd) . '<br>'; }
        if (!isset($name) || !isset($pwd)) {
            session_destroy();
        } else {
            $result = $db->query('SELECT * FROM ' . $uni . '_Users WHERE LOWER(username) = \'' . $name . '\'');
            $u = $result->fetch_object();
            if ($debug) { print_r($u); echo '<br>'; }
            if (is_null($u) || strcmp($u->password, sha1($pwd)) != 0) {
                session_destroy();
            } else {
                if ($debug) { echo 'Creating Session Variables<br>'; }
                session_regenerate_id(true);
                $_SESSION['user'] = $u->username;
                if ($u->user_id) { $_SESSION['id'] = $u->user_id; }
                if ($u->security) { $_SESSION['security'] = $u->security; }
                if ($u->login) { $_SESSION['login'] = $u->login; }
                if ($u->loaded) { $_SESSION['loaded'] = $u->loaded; }
                if ($u->faction) { $_SESSION['faction'] = $u->faction; }
                if ($u->syndicate) { $_SESSION['syndicate'] = $u->syndicate; }
                if ($u->rank) { $_SESSION['rank'] = $u->rank; }
                if ($u->comp) { $_SESSION['comp'] = $u->comp; }
                if ($u->imagepack) { setcookie("imagepack", $u->imagepack, time() + 60 * 60 * 24 * 365, "/"); }
                $db->query('UPDATE ' . $uni . '_Users SET login = UTC_TIMESTAMP() WHERE LOWER(username) = \'' . $name . '\'');
            }
        }
    }
    session_write_close();
    $url = $db->real_escape_string($_REQUEST['url']);
    if ($debug) { echo $url . '<br>'; }
    $db->close();
    if (strpos($url, $base_url) === false) { $url = $base_url . '/' . $uni . '/index.php'; }
    if (!$debug) { header("Location: $url"); }
} else {
    $signedup = 0;
    $alreadysignedup = 0;
    $url = null;
    if (isset($_REQUEST['signedup'])) { $signedup = 1; $url = $db->real_escape_string($_REQUEST['url']); }
    if (isset($_REQUEST['alreadysignedup'])) { $alreadysignedup = 1; $url = $db->real_escape_string($_REQUEST['url']); }
    if (is_null($url)) { $url = $_SERVER['HTTP_REFERER']; }
    $db->close();
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html" />
            <meta http-equiv="Content-Style-Type" content="text/css" />
            <meta http-equiv="Content-Script-Type" content="text/javascript" />
            <title>Tightwad's Pardus Map Log In Page</title>
            <link rel="stylesheet" type="text/css" href="<?php echo $css; ?>" />
            <script type="text/javascript">
                var _gaq = _gaq || [];
                _gaq.push(['_setAccount', 'UA-15475436-5']);
                _gaq.push(['_setDomainName', '.mhwva.net']);
                _gaq.push(['_trackPageview']);

                (function() {
                    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                })();
            </script>    
        </head>
        <body bgcolor="#ffffff" text="#000000" link="#000000" vlink="#000000" alink="#0000FF">
            <div id="header_side"><?php include('include/header_side.php'); ?></div>
            <div id="body">
                <h2>Log In</h2>
                <?php if ($signedup) { echo '<h3>Your Account has been created Please Log in.</h3>'; } ?>
                <?php if ($alreadysignedup) { 
                    echo '<h3>The Account you tried to setup was already Created.</h3>'; 
                    echo '<h3>Please PM ';
                    if ($uni == 'Orion') { echo 'Tightwad'; }
                    if ($uni == 'Artemis') { echo 'Spendthrift'; }
                    if ($uni == 'Pegasus') { echo 'Zaqwer (Retired)'; }
                    echo ' To get it resolved</h3>';
                } ?>
                <form method="POST" action="<?php echo $base_url . '/' . $uni; ?>/login.php">
                    Username: <input type="text" id="username" name="username" size="20"><br><br>
                    Password : <input type="password" name="password" size="20"><br><br>
                    <input type="hidden" value="<?php echo $url; ?>" name="url">
                    <input type="submit" value="Login" name="login">
                </form>
                <br>
                <h2>Sign Up for free Account</h2>
                <form method="POST" action="<?php echo $base_url . '/' . $uni; ?>/signup.php">
                    <input type="hidden" value="<?php echo $url; ?>" name="url">
                    <input type="submit" value="Sign Up" name="start">
                </form>
                <br>            
            </div>
        </body>
    </html>
<?php
}
?>

