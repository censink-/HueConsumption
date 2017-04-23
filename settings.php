<?php
include_once 'includes/db.php';

if (isset($_POST['submit']))
{
	$ip = mysqli_real_escape_string($db, $_POST['ip']);
	$username = mysqli_real_escape_string($db, $_POST['username']);


	$updatesettings = mysqli_query($db, "UPDATE users SET `hue_ip` = '" . $ip . "', `hue_username` = '" . $username . "' WHERE `id` = 1;");

	if (!$updatesettings)
	{
		die(mysqli_error($db));
	}
	else
	{
		header("Location: settings.php");
		exit;
	}
}
else
{
	$getsettings = mysqli_query( $db, "SELECT * FROM users WHERE `id` = 1 LIMIT 1");

	if( !$getsettings )
	{
		echo mysqli_error( $db );
	}
	$settings = mysqli_fetch_assoc( $getsettings );

	$input_ip = "";
	$input_username = "";
	if( $settings['hue_ip'] != null )
	{
		$input_ip = $settings['hue_ip'];
	}
	if ($settings['hue_username'] != null)
	{
		$input_username = $settings['hue_username'];
	}
}
?>
<html>
	<?php include_once 'includes/header.php'; ?>
	<body>
		<?php include_once 'includes/navigation.php'; ?>
		<div class="container">
			<h1>Find Hue bridge</h1>
			<hr>
			<form action="settings.php" method="post" class="col-sm-4">
				<div class="form-group">
					<label for="input-ip">Hue Bridge IP</label>
					<input type="text" id="input-ip" value="<?= $input_ip ?>" name="ip" class="form-control">
				</div>
				<div class="form-group">
					<label for="input-username">Hue API Username</label>
					<input type="text" id="input-username" value="<?= $input_username ?>" name="username" class="form-control">
				</div>
				<input type="submit" class="btn btn-success" name="submit" value="Save">
			</form>
		</div>
		<?php include_once 'includes/scripts.php'; ?>
	</body>
</html>