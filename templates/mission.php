<?php
declare(strict_types=1);
/** @var string $base_url */
/** @var string $uni */
/** @var string $css */
/** @var string $m_css */
/** @var string $faction */
/** @var string $syndicate */
/** @var array $mission_list */
use Pardusmapper\Core\Settings;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<title>
		<?php echo $uni; ?>
		<?php if (isset($cluster)) { echo ' ' . $cluster; } ?>
		<?php if (isset($s)) { echo ' ' . $s->name; } ?>
		<?php //if ($x1 && $y1) { echo ' ' . $loc->name; } // this doesn't seem defined ?>
		<?php echo '\'s Mission Listing'; ?>
		</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo $m_css; ?>" />
		<script type="text/javascript" src="<?php echo $base_url; ?>/resources/main.js"></script>
		<script language="Javascript">
			//<![CDATA[
			function getCheckedValue(radioObj) {
				if(!radioObj)
					return "all";
				var radioLength = radioObj.length;
				if(radioLength == undefined)
					if(radioObj.checked)
						return radioObj.value;
					else
						return "";
				for(var i = 0; i < radioLength; i++) {
					if(radioObj[i].checked) {
						return radioObj[i].value;
					}
				}
				return "";
			}
			function multiSort(value) {
				var i = sort_var.indexOf(value);
				if (i == -1) {
					sort_var += value;
				}
				if (value == "A") {
					if (sort_order & 1) { sort_order -= 1; }
					else { sort_order += 1; }
				}
				if (value == "B") {
					if (sort_order & 2) { sort_order -= 2; }
					else { sort_order += 2; }
				}
				if (value == "C") {
					if (sort_order & 4) { sort_order -= 4; }
					else { sort_order += 4; }
				}
				if (value == "D") {
					if (sort_order & 8) { sort_order -= 8; }
					else { sort_order += 8; }
				}
				if (value == "E") {
					if (sort_order & 16) { sort_order -= 16; }
					else { sort_order += 16; }
				}
				if (value == "F") {
					if (sort_order & 32) { sort_order -= 32; }
					else { sort_order += 32; }
				}
				if (value == "G") {
					if (sort_order & 64) { sort_order -= 64; }
					else { sort_order += 64; }
				}
				if (value == "H") {
					if (sort_order & 128) { sort_order -= 128; }
					else { sort_order += 128; }
				}
				if (value == "I") {
					if (sort_order & 256) { sort_order -= 256; }
					else { sort_order += 256; }
				}
				if (value == "J") {
					if (sort_order & 512) { sort_order -= 512; }
					else { sort_order += 512; }
				}
				if (value == "K") {
					if (sort_order & 1024) { sort_order -= 1024; }
					else { sort_order += 1024; }
				}
				loadMission(mission,0);
			}
			function removeSort(value) {
				var i = sort_var.indexOf(value);
				if (i !== false) {
					sort_var = sort_var.substr(0,i) + sort_var.substr(i+1);
				} else { sort_var = ''; }
				if (value == "A") {
					if (sort_order & 1) { sort_order -= 1; }
				}
				if (value == "B") {
					if (sort_order & 2) { sort_order -= 2; }
				}
				if (value == "C") {
					if (sort_order & 4) { sort_order -= 4; }
				}
				if (value == "D") {
					if (sort_order & 8) { sort_order -= 8; }
				}
				if (value == "E") {
					if (sort_order & 16) { sort_order -= 16; }
				}
				if (value == "F") {
					if (sort_order & 32) { sort_order -= 32; }
				}
				if (value == "G") {
					if (sort_order & 64) { sort_order -= 64; }
				}
				if (value == "H") {
					if (sort_order & 128) { sort_order -= 128; }
				}
				if (value == "I") {
					if (sort_order & 256) { sort_order -= 256; }
				}
				if (value == "J") {
					if (sort_order & 512) { sort_order -= 512; }
				}
				if (value == "K") {
					if (sort_order & 1024) { sort_order -= 1024; }
				}
				loadMission(mission,0);
			}
			function updateMission() {
				loadMission(mission,0);
			}
			function loadMission(m_key,reset) {
				if (reset) {
					sort_var = "";
					sort_order = 0;
				}
				mission = m_key;
				var faction = getCheckedValue(document.getElementById('limit').limit);
				var mode = getCheckedValue(document.getElementById('limit').mode);
				var url = <?php echo '"' . $base_url . '"'; ?> + "/info/missions.php";
				var params = "uni=" + <?php echo '"' . $uni . '"'; ?> + "&sort=" + sort_var + "&order=" + sort_order + "&type=" + mission + "&faction=" + faction + "&mode=" + mode + "&pilot=" + pilot + "&pilot_s=" + pilot_s;
				<?php if (isset($cluster)) { echo 'params += "&cluster=" + \'' . $cluster . '\';'; } ?>
				<?php if (isset($s)) { echo 'params += "&sector=" + \'' . $s->name . '\';'; } ?>
				<?php if (isset($id)) { echo 'params += "&loc=" + \'' . $id . '\';'; } ?>


				bodyhttp.open("POST",url,true);
				bodyhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				//bodyhttp.setRequestHeader("Content-length", params.length);
				//bodyhttp.setRequestHeader("Connection" , "close");

				bodyhttp.onreadystatechange = function () {
					if (bodyhttp.readyState == 4) {
						var el = document.getElementById("mission_body");
						el.innerHTML = bodyhttp.responseText;
					} else {
						document.getElementById("mission_body").innerHTML = "<img src=\"<?= Settings::$IMG_DIR_MAPPER ?>/ajax-loader.gif\" />";
					}
				}
				bodyhttp.send(params);
			}

			var sort_var = "";
			var sort_order = 0;		
			var mission = 'all';
			var faction = 1;
			var pilot = "";
			var pilot_s = "";
			<?php 
				if (!is_null($faction) && strpos((string) $faction,'_uni_')) { echo 'pilot = \'uni\';'; }
				if (!is_null($faction) && strpos((string) $faction,'_emp_')) { echo 'pilot = \'emp\';'; }
				if (!is_null($faction) && strpos((string) $faction,'_fed_')) { echo 'pilot = \'fed\';'; }
				if (!is_null($syndicate) && strpos((string) $syndicate,'tss')) { echo 'pilot_s = \'tss\';'; }
				if (!is_null($syndicate) && strpos((string) $syndicate,'eps')) { echo 'pilot_s = \'eps\';'; }
			?>
			var bodyhttp = getXMLHttpObject();
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
	<body>
		<div id="header_side"><?php require_once(templates('header_side')); ?></div>
		<div id="mission_list" onmouseover="this.style.zIndex=20" onmouseout="this.style.zIndex=0">
			<?php 
				echo '<table class="messagestyle">';
				echo '<tr><th>Mission Types</th></tr>';
				foreach ($mission_list as $n) {
					echo '<tr><td><a href="#" onClick="loadMission(\'' . $n . '\',1);">' . $n . '</a></td></tr>';				
				}
				echo '</table>';
			?>
		</div>
		<div id="body">
			<div id="mission_limit">
				<form id="limit">
					<label for="limit0"><input id="limit0" type="radio" onclick="updateMission()" name="limit" value="0" checked>No Limit</label>
					<label for="limit1"><input id="limit1" type="radio" onclick="updateMission()" name="limit" value="1">Limit By Faction</label>
					<label for="limit2"><input id="limit2" type="radio" onclick="updateMission()" name="limit" value="2">Limit By Neutral</label>
					<label for="limit3"><input id="limit3" type="radio" onclick="updateMission()" name="limit" value="3">Limit By Syndicate</label>
					<br>
					<label for="mode1"><input id="mode1" type="radio" onclick="updateMission()" name="mode" value="1" checked>Comp/Rank Limited</label>
					<label for="mode0"><input id="mode0" type="radio" onclick="updateMission()" name="mode" value="0">All Missions</label>
				</form>
			</div>
			<div id="mission_body"></div>
		</div>
		<div id="footer"><?php require_once(templates('footer')); ?></div>
	<script type="text/javascript">var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));</script>
	<script type="text/javascript">try {	var pageTracker = _gat._getTracker("UA-15475436-1");	pageTracker._trackPageview(); } catch(err) {}</script>
	</body>
</html>
