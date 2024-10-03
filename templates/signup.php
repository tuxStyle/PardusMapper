<?php 
declare(strict_types=1); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<title>Tightwad's Pardus Map Sign Up Page</title>
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
	<body>
		<div id="header_side">
				<?php require_once(templates('header_side')); ?>
		</div>
		<div id="body">
			<h2>Sign Up</h2>
			<h3>You must have Tightwad's Pardus Mapper User Script installed and uploaded data before signing up for an Account</h3>
			<form method="POST" action="<?php echo $base_url . '/' . $uni; ?>/signup.php">
				<?php if ($invalidusr) {echo 'Invalid User Name. Please Enter Your Pardus Name Exactly.<br>'; } ?>
				Username: <input type="text" id="username" name="username" size="20" value="<?php echo $name; ?>"><br><br>
				<?php if($invalidpwd) {echo 'Your Passwords Did Not Match Please Try Again.<br>'; } ?>
				Password : <input type="password" name="password1" size="20"><br><br>
				Password:  <input type="password" name="password2" size="20"><br><br>
				<input type="hidden" value="<?php echo $url; ?>" name="url">
				<input type="submit" value="Sign Up" name="signup">
			</form>
			<br>
		</div>
	</body>
</html>
