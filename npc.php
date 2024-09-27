<?php
require_once('include/mysqldb.php');
$db = new mysqldb();  // Create an instance of the Database class

// Set Univers Variable and Session Name
if (!isset($_REQUEST['uni'])) { exit; }

session_name($uni = $db->real_escape_string($_REQUEST['uni']));

$testing = Settings::TESTING;
$debug = Settings::DEBUG;

if ($testing || $debug) {
	error_reporting(E_STRICT | E_ALL | E_NOTICE);
}

$base_url = 'https://pardusmapper.com';
if ($testing) { $base_url .= '/TestMap'; }

$css = $base_url . '/main.css';
$n_css = $base_url. '/npc.css';

// Start the Session
session_start();

$security = 0;
if (isset($_SESSION['security'])) { $security = $db->real_escape_string($_SESSION['security']); }

$img_url = Settings::IMG_DIR;
if (isset($_COOKIE['imagepack'])) {
    $img_url = $_COOKIE['imagepack'];
    
    // Check if the last character is not a '/'
    if ($img_url[strlen($img_url) - 1] != '/') {
        $img_url .= '/';  // Append '/' if not already present
    }
}


$cluster = "";
$sector = "";

if (isset($_REQUEST['cluster'])) {
	$cluster = $db->real_escape_string(urldecode($_REQUEST['cluster']));
}
if (isset($_REQUEST['sector'])) {
    // Escape and decode the sector parameter
    $sector = $db->real_escape_string(urldecode($_REQUEST['sector']));
    
    // Query to fetch the cluster data using a parameterized query
    $db->query("SELECT * FROM Pardus_Clusters WHERE c_id = 
                (SELECT c_id FROM Pardus_Sectors WHERE name = '$sector')");
    
    // Fetch the result
    $cluster = $db->nextObject();
    
    // Check if $cluster is an object before accessing the 'code' property
    if ($cluster) {
        $clusterCode = $cluster->code;  // Store the 'code' property in a separate variable
    } else {
        // Handle the case where no cluster was found
        echo "No cluster found for sector: " . htmlspecialchars($sector);
    }
}


$db->close();
$db = null;

$today = date("Y-m-d");

if (isset($_SESSION['loaded']) && ((strtotime($today) - strtotime($_SESSION['loaded'])) < 172800)) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<?php if (isset($_REQUEST['sector'])) { echo '<title>' . $sector . '\'s NPC Listing</title>'; } ?>
		<?php if (isset($_REQUEST['cluster'])) { echo '<title>' . $cluster . '\'s NPC Listing</title>'; } ?>
		<link rel="stylesheet" type="text/css" href="<?= $css; ?>" />
		<link rel="stylesheet" type="text/css" href="<?= $n_css; ?>" />
		<script type="text/javascript" src="<?= $base_url; ?>/include/main.js"></script>
		<script language="Javascript">
			//<![CDATA[
			function loadList() {
				updateList();
				setTimeout('loadList()',60000);
			}
			function updateList() {
				var url = "<?= $base_url ?>/info/npc_list.php";
				var params = "uni=<?= $uni ?>";
				<?php if (isset($_REQUEST['cluster'])) { echo 'params += "&cluster=" + "' . $cluster . '";'; } ?>
				<?php if (isset($_REQUEST['sector'])) { echo 'params += "&sector=" + "' . $sector . '";'; } ?>
				listhttp.open("POST",url,true);
				listhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

				listhttp.onreadystatechange = function () {
					if (listhttp.readyState == 4) {
						var el = document.getElementById("npc_list");
						el.innerHTML = listhttp.responseText;
					}
				}
				listhttp.send(params);
			}
			function multiSort(value) {
				var i = sort_var.indexOf(value);
				if (i == -1) {
					sort_var += value;
				}
				if (value == "C") {
					if (sort_order & 1) { sort_order -= 1; }
					else { sort_order += 1; }
				}
				if (value == "S") {
					if (sort_order & 2) { sort_order -= 2; }
					else { sort_order += 2; }
				}
				if (value == "L") {
					if (sort_order & 4) { sort_order -= 4; }
					else { sort_order += 4; }
				}
				if (value == "N") {
					if (sort_order & 8) { sort_order -= 8; }
					else { sort_order += 8; }
				}
				if (value == "A") {
					if (sort_order & 16) { sort_order -= 16; }
					else { sort_order += 16; }
				}
				if (value == "T") {
					if (sort_order & 32) { sort_order -= 32; }
					else { sort_order += 32; }
				}
				loadNPC(npc,0);
			}
			function removeSort(value) {
				var i = sort_var.indexOf(value);
				if (i !== false) {
					sort_var = sort_var.substr(0,i) + sort_var.substr(i+1);
				} else { sort_var = ''; }
				if (value == "C") {
					if (sort_order & 1) { sort_order -= 1; }
				}
				if (value == "S") {
					if (sort_order & 2) { sort_order -= 2; }
				}
				if (value == "L") {
					if (sort_order & 4) { sort_order -= 4; }
				}
				if (value == "N") {
					if (sort_order & 8) { sort_order -= 8; }
				}
				if (value == "A") {
					if (sort_order & 16) { sort_order -= 16; }
				}
				if (value == "T") {
					if (sort_order & 32) { sort_order -= 32; }
				}
				loadNPC(npc,0);
			}
			function loadNPC(key,reset) {
				if (reset) {
					sort_var = "";
					sort_order = 0;
				}
				closeDetail();
				npc = key;
				
				var url = "<?= $base_url ?>/info/npc.php";
				var params = "uni=<?= $uni ?>&sort=" + sort_var + "&order=" + sort_order + "&npc=" + npc;
				<?php if (isset($_REQUEST['cluster'])) { echo 'params += "&cluster=" + \'' . $cluster . '\''; } ?>
				<?php if (isset($_REQUEST['sector'])) { echo 'params += "&sector=" + \'' . $sector . '\''; } ?>

				bodyhttp.open("POST",url,true);
				bodyhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				bodyhttp.onreadystatechange = function () {
					if (bodyhttp.readyState == 4) {
						document.getElementById("npc_body").innerHTML = bodyhttp.responseText;
					} else {
						document.getElementById("npc_body").innerHTML = "<img src=\"https://pardusmapper.com/images/ajax-loader.gif\" />";
					}
				}
				bodyhttp.send(params);
			}

			var sort_var = "";
			var sort_order = 0;
			var npc = 'all';
			var bodyhttp = getXMLHttpObject();
			var listhttp = getXMLHttpObject();
			
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
	<body onload="loadList(); loadNPC('All',1);">
		<div id="header_side"><?php include('include/header_side.php'); ?></div>
		<div id="npc_list"></div>
		<div id="body">
			<div id="npc_body"></div>
			<div id="details" name="npc">
				<div id="close_detail"><center><h3><a href=# onClick="closeDetail();return false;">Close Detail</a></h3></center></div>
				<div id="d_con"></div>
			</div>
		</div>
		<div id="footer"><?php include('include/footer.php'); ?></div>
	<script type="text/javascript">var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));</script>
	<script type="text/javascript">try {	var pageTracker = _gat._getTracker("UA-15475436-1");	pageTracker._trackPageview(); } catch(err) {}</script>
	</body>
</html>

<?php } else { ?>

<html>
	<head>
		<?php if (isset($_REQUEST['sector'])) { echo '<title>' . $sector . '\'s NPC Listing</title>'; } ?>
		<?php if (isset($_REQUEST['cluster'])) { echo '<title>' . $cluster . '\'s NPC Listing</title>'; } ?>
		<link rel="stylesheet" type="text/css" href="<?= $css; ?>" />
		<link rel="stylesheet" type="text/css" href="<?= $n_css; ?>" />
	</head>
	<body>
		<div id="header_side"><?php include('include/header_side.php'); ?></div>
		<div id="close_detail"><center><h3><a href=# onClick="closeDetail();return false;">Close Detail</a></h3></center></div>
		<div id="npc_list"></div>
		<div id="body">
			<center>
				<h2> Please Log In to View NPC Information </h2>
				<br>
				<h2>To Setup an Account all you need to do is install the script, then log into Pardus or Refresh your Pardus Page.
					<br>
					Once you have done that goto the "Log In" page and follow the "Sign Up" link to create your account.
					<br>
					If you are having trouble PM to
				<?php
					if ($uni == 'Orion') { echo 'Tightwad'; }
					if ($uni == 'Artemis') { echo 'Spendthrift'; }
				?>
					ingame for assistance.
				</h2>
			</center>
		</div>
		<div id="footer"><?php include('include/footer.php'); ?></div>
	</body>
</html>
<?php } ?>
