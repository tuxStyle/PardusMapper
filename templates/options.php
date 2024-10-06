<?php
declare(strict_types=1);
/** @var string $base_url */
/** @var string $css */
/** @var string $uni */
/** @var string $url */
/** @var string $invalidoldpwd */
/** @var string $invalidpwd */
/** @var string $loc2 */
/** @var string $loc3 */
/** @var string $logged_in */
/** @var string $newpwdnotentered */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<title>Tightwad's Pardus Map Options Page</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $css; ?>" />
		<script type="text/javascript">
			//<![CDATA[
			function loadLoc() {
				var img_pack = document.getElementById('img_pack');
				switch(img_pack.loc1.value) {
					case "1" : {
						img_pack.loc2[1].selected = "1";
						img_pack.loc3.value = "static.pardus.at/images";
						break;
					}
					case "2" : {
						img_pack.loc2[1].selected = "1";
						img_pack.loc3.value = "static.pardus.at/img/std";
						break;
					}
					case "3" : {
						img_pack.loc2[1].selected = "1";
						img_pack.loc3.value = "static.pardus.at/img/stdhq";
						break;
					}
					case "4" : {
						img_pack.loc2[1].selected = "1";
						img_pack.loc3.value = "static.pardus.at/img/kora";
						break;
					}
					case "5" : {
						img_pack.loc2[1].selected = "1";
						img_pack.loc3.value = "static.pardus.at/img/solarix";
						break;
					}
				}
			}
			//]]>
		</script>
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
		<center>
			<?php if ($logged_in) { ?>
			<h2>Change Password</h2>
			<form method="POST" action="<?php echo $base_url . '/' . $uni; ?>/options.php">
				<?php if ($invalidoldpwd) {echo 'Your Old Password was Incorrect Please Try Again.<br>'; } ?>
				Old Password : <input type="password" name="oldpwd" size="20"><br><br>
				<?php if($invalidpwd) {echo 'Your Passwords Did Not Match Please Try Again.<br>'; } ?>
				<?php if($newpwdnotentered) { echo 'You Need to Enter a New Password.<br>'; } ?>
				New Password : <input type="password" name="newpwd1" size="20"><br><br>
				New Password:  <input type="password" name="newpwd2" size="20"><br><br>
				<input type="hidden" value="<?php echo $url; ?>" name="url">
				<input type="submit" value="Change Password" name="change">
			</form>
			<br>
			<?php } ?>
			<?php if (isset($_REQUEST['image'])) { echo '<h3>Image Pack Set to ' . $loc2 . $loc3 . '</h3>'; } ?>
			<h2>Image Pack</h2>
			<form id="img_pack" method="POST" action="<?php echo $base_url . '/' . $uni; ?>/options.php">
				<select onchange="loadLoc()" name="loc1">
					<option value="0">Custom</option>
					<option value="1">Clasic</option>
					<option value="2">Standard</option>
					<option value="3">Standard HQ</option>
					<option value="4">Kora's IP</option>
					<option value="5">Solarix's IP</option>
				</select>
				<br><br>
				<select name="loc2">
					<option value="file://">file://</option>
					<option value="https://">https://</option>
				</select>
				<input type="text" name="loc3" value="" size="30">
				<br><br>
				<input type="hidden" value="<?php echo $url; ?>" name="url">
				<input type="submit" value="Set Image Pack" name="image">
			</form>
			<br><br>
			<a href="<?php echo $url; ?>">Return to Previous Page</a>
		</center>
		</div>
	</body>
</html>
