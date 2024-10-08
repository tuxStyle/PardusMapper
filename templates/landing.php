<?php
declare(strict_types=1);
use \Pardusmapper\Core\Settings;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<title>Pardus Image Map</title>
		<link rel="stylesheet" type="text/css" href="<?= Settings::$BASE_URL ?>/resources/main.css" />
		<link rel="stylesheet" type="text/css" href="<?= Settings::$BASE_URL ?>/resources/index.css" />

<style type="text/css">
body {
background-color:#152F2F;
background-image:url('<?= Settings::$BASE_URL ?>/images/bback.png');
background-repeat:repeat-x;
background-position:center;
background-attachment:fixed;
font-size:1em;
}
</style>
	</head>

<body>

<div id="choose-uni">
<div>
<div id="chead"><center>Zaqwer's Pardus Mapper (copy by Tightwad)</center>Choose your Universe map:</div>
<div id="cmain">
<a href="<?= Settings::$BASE_URL ?>/Orion" style="background-image:url(https://static.pardus.at/various/universes/orion_64x64.png)">Orion Universe Map</a>
<a href="<?= Settings::$BASE_URL ?>/Artemis" style="background-image:url(https://static.pardus.at/various/universes/artemis_64x64.png)">Artemis Universe Map</a>
<a href="<?= Settings::$BASE_URL ?>/Pegasus" style="background-image:url(https://static.pardus.at/various/universes/pegasus_64x64.png)">Pegasus Universe Map</a>
<div class="helper-links">
<a href="http://pardusmap.mhwva.net/Download/ZaqwersPardusMapDB.sql">Download Zaqwers Pardus Map DataBase</a>
<a href="http://pardusmap.mhwva.net/Download/ZaqwersPardusMapWebSite.zip">Download Zaqwers Pardus Map WebSite</a>
<a href="http://pardusmap.mhwva.net/Download/ZaqwersPardusTestMapWebSite.zip">Download Zaqwers Pardus Map Test Web Site</a>
</div>
</div>
</div>
</div>

</body>
</html>
