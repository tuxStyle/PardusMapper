<?php
declare(strict_types=1);
/** @var string $base_url */
/** @var string $css */
/** @var string $uni */
/** @var string $url */
/** @var string $signedup */
/** @var string $alreadysignedup */
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
        <div id="header_side"><?php require_once(templates('header_side')); ?></div>
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