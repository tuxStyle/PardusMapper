<?php
declare(strict_types=1);
/** @var string $base_url */
/** @var string $css */
/** @var string $r_css */
/** @var string $uni */
/** @var string $sector */
/** @var string $pilot */
/** @var int $security */
/** @var array $res_list */
use Pardusmapper\Core\Settings;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<title><?php echo $pilot; ?>'s Upkeep Tables</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo $r_css; ?>" />
		<script type="text/javascript" src="<?php echo $base_url; ?>/resources/main.js"></script>
		<script language="Javascript">
			//<![CDATA[
			function multiSort(value) {
				var i = sort_var.indexOf(value);
				if (i == -1) {
					sort_var += value;
				}
				if (value == "L") {
					if (sort_order & 1) { sort_order -= 1; }
					else { sort_order += 1; }
				}
				if (value == "B") {
					if (sort_order & 2) { sort_order -= 2; }
					else { sort_order += 2; }
				}
				if (value == "S") {
					if (sort_order & 4) { sort_order -= 4; }
					else { sort_order += 4; }
				}
				if (value == "T") {
					if (sort_order & 8) { sort_order -= 8; }
					else { sort_order += 8; }
				}
				<?php if ($security == 1 || $security == 100) { ?>
					if (value == "O") {
						if (sort_order & 16) { sort_order -= 16; }
						else { sort_order += 16; }
					}
					if (value == "A") {
						if (sort_order & 32) { sort_order -= 32; }
						else { sort_order += 32; }
					}
				<?php } ?>
				updateResources(resource,0);
			}
			function removeSort(value) {
				var i = sort_var.indexOf(value);
				if (i !== false) {
					sort_var = sort_var.substr(0,i) + sort_var.substr(i+1);
				} else { sort_var = ''; }
				if (value == "L") {
					if (sort_order & 1) { sort_order -= 1; }
				}
				if (value == "B") {
					if (sort_order & 2) { sort_order -= 2; }
				}
				if (value == "S") {
					if (sort_order & 4) { sort_order -= 4; }
				}
				if (value == "T") {
					if (sort_order & 8) { sort_order -= 8; }
				}
				<?php if ($security == 1 || $security == 100) { ?>
					if (value == "O") {
						if (sort_order & 16) { sort_order -= 16; }
					}
					if (value == "A") {
						if (sort_order & 32) { sort_order -= 32; }
					}
				<?php } ?>
				updateResources(resource,0);
			}
			function loadResources() {
				updateResources(resource,1);
				//setTimeout('updateResources(resource,0)',60000);
			}
			function updateResources(key,reset) {
				if (reset) {
					sort_var = "";
					sort_order = 0;
				}
				closeDetail();
				resource = key;
				var url = <?php echo '"' . $base_url . '"'; ?> + "/info/resources.php";
				var params = "uni=" + <?php echo '"' . $uni . '"'; ?> + "&sector=" + <?php echo '"' . $sector . '"'; ?> + "&resource=" + resource + "&sort=" + sort_var + "&order=" + sort_order + "&pilot=" + <?php echo '"' . $pilot . '"'; ?>;
				xmlhttp.open("POST",url,true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				//xmlhttp.setRequestHeader("Content-length", params.length);
				//xmlhttp.setRequestHeader("Connection" , "close");

				xmlhttp.onreadystatechange = function () {
					if (xmlhttp.readyState == 4) {
						document.getElementById("resource_body").innerHTML = xmlhttp.responseText;
					} else {
						document.getElementById("resource_body").innerHTML = "<img src=\"<?= Settings::$IMG_DIR_MAPPER ?>/ajax-loader.gif\" />";
					}
				}
				xmlhttp.send(params);
			}
			var sort_var = '';
			var sort_order = 0;
			var resource = 'all';
			var xmlhttp = getXMLHttpObject();
			var detailhttp = getXMLHttpObject();
			window.onload=loadResources;
			//]]>
		</script>
		<?php if (isset($id) && $id > 0) { ?>
		<script language="Javascript">
			function addInterest(box,uni,id,loc) {
				var http = getXMLHttpObject();
				var url = <?php echo '"' . $base_url . '"'; ?> + "/info/addinterest.php";
				if (box.checked === true) { var params = "uni=" + uni + "&id=" + id + "&loc=" + loc + "&add=1"; }
				else { var params = "uni=" + uni + "&id=" + id + "&loc=" + loc; }
				http.open("POST",url,true);
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.setRequestHeader("Content-length", params.length);
				http.setRequestHeader("Connection" , "close");

				http.onreadystatechange = function () {
					if (http.readyState == 4) {
					}
				}
				http.send(params);


			}
		</script>
		<?php } ?>
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
	<body>
		<div id="header_side"><?php require_once(templates('header_side')); ?></div>
		<div id="body">
			<div id="resource_list">
				<table>
					<tr><th colspan="2">Resource</th></tr>
					<?php foreach ($res_list as $key) { echo '<tr><td><a href="#" onClick="updateResources(\'' . $key . '\',1);return false;">' . $key . '<a></td></tr>'; }?>
				</table>
			</div>
			<div id="resource_body"></div>
			<div id="details" name="resources">
				<div id="close_detail"><center><h3><a href=# onClick="closeDetail();return false;">Close Detail</a></h3></center></div>
				<div id="d_con"></div>
			
			</div>
		</div>
		<div id="footer"><?php require_once(templates('footer')); ?></div>
	</body>
</html>
