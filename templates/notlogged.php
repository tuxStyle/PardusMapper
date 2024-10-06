<?php
declare(strict_types=1);

/** @var string $css */
/** @var string $m_css */
/** @var string $uni */

?>
<html>
	<head>
		<?php if (isset($s)) { echo '<title>' . $s->name . '\'s Mission Listing</title>'; } ?>
		<?php if (isset($cluster)) { echo '<title>' . $cluster . '\'s Mission Listing</title>'; } ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $css; ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo $m_css; ?>" />
	</head>
	<body>
		<div id="header_side"><?php require_once(templates('header_side')); ?></div>
		<div id="body">
			<center>
				<h2> Please Log In to View Mission Information </h2>
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
		<div id="footer"><?php require_once(templates('footer')); ?></div>
	</body>
</html>