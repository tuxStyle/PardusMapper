<?php 
declare(strict_types=1); 
use Pardusmapper\Core\Settings;
?>
<html>
	<head>
		<title>Download User Script</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $css; ?>" />
	</head>
	<body>
		<div id="header_side"><?php require_once(templates('header_side')); ?></div>
		<div id="footer"><?php require_once(templates('footer.php')); ?></div>
		<div id="body">
		<h1>*** Note Greasemonkey 4 and Firefox 57 have caused myscript to stop working ***</h1>
		<ul>
			<li><h1>Firefox</h1>
			<ul>
				<h2>
				<li>For Firefox version 57 or higher you will need to use <a href="https://addons.mozilla.org/en-US/firefox/addon/tampermonkey/">Tamper Monkey</a> instead of  <a href="https://addons.mozilla.org/en-US/firefox/addon/748">Greasemonkey</a> to use my script.
				</h2>
			</ul>
			<li><h1>Opera</h1>
			<ul>
				<h2>
				<li>I am currently not actively trying to get the script to work with Opera and the below directions are old.
				<li>For help using User Scripts in Opera follow this <a href="http://www.opera.com/browser/tutorials/userjs/using/">Link</a>
				<li>You will need to install two other User Scripts before mine will work they are
					<a href="<?= Settings::$BASE_URL ?>/Download/a-lib-stacktrace.js">a-lib-stacktrace.js</a>
					and
					<a href="<?= Settings::$BASE_URL ?>/Download/a-lib-xmlhttp-cd.js">a-lib-xmlhttp-cd.js</a>
				</h2>
			</ul>
			<li><h1>Google Chrome</h1>
			<ul>
				<h2>
				<li>Install <a href="http://tampermonkey.net/">Tamper Monkey</a>
				</h2>
			</ul>
			<li><h1>Once you have done one of the above steps you can download my User Script <a href="<?= Settings::$BASE_URL ?>/Download/pardus_mapper.user.js">Here</a></h1>
		</ul>
		</div>
	</body>
</html>