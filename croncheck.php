<?php
$logfile = fopen('cronlog.txt', 'a');
fwrite($logfile, "\n[" . date("Y-m-d H:i:s") . "] Cronjob ran!");

include_once 'includes/db.php';

$get_api_details = mysqli_query($db, "SELECT hue_ip, hue_username FROM users WHERE `id` = 1");
if (!$get_api_details)
{
	fwrite($logfile, "\n[" . date("Y-m-d H:i:s") . "] SQL FAILED: " . mysqli_error($db));
}
$api_details = mysqli_fetch_assoc($get_api_details);

$api_ip = $api_details['hue_ip'];
$api_username = $api_details['hue_username'];
$api_url = "";
$api_response = "";
$error = "";

if ($api_ip == "")
{
	$error = "No Hue Bridge!";
}
else if ($api_username == "")
{
	$error = "No Hue Username";
}

if ($error == "") {
	$api_url = "http://" . $api_ip . "/api/" . $api_username . "/";
	$api_call = file_get_contents($api_url . "lights");
	if (!$api_call)
	{
		$error = "Can't reach Bridge API";
	}
	else
	{
		$api_response = json_decode($api_call, true);

		foreach($api_response as $key => $light)
		{
			$state = $light['state'];
			if ($state['on'] == true)
			{
				$on = 1;
			} else
			{
				$on = 0;
			}
			if ($on == 1 && $state['reachable'] == true && $state['bri'] != 0)
			{
				$brightness = $state['bri'];
			} else
			{
				$brightness= 0;
			}
			fwrite($logfile, "\n[" . date("Y-m-d H:i:s") . "] SQL " . $key . ": INSERT INTO hue_entries VALUES (null, 1, '" . $light['uniqueid'] . "', '" . $light['name'] . "', " . $on . ", " . $state['hue'] + 0 . ", " . $state['sat'] + 0 . ", " . $brightness . ", null);");

			$addentry = mysqli_query($db, "INSERT INTO hue_entries VALUES (null, 1, '" . $light['uniqueid'] . "', '" . $light['name'] . "', " . $on . ", " . ($state['hue'] + 0) . ", " . ($state['sat'] + 0) . ", " . $brightness . ", null);");
			if ($addentry)
			{
				fwrite($logfile, "\n[" . date("Y-m-d H:i:s") . "] SQL " . $key . " gucci");
			}
			else
			{
				fwrite($logfile, "\n[" . date("Y-m-d H:i:s") . "] SQL " . $key . " FAILED: " . mysqli_error($db));
			}
		}
	}
} else
{
	fwrite($logfile, "\n[" . date("Y-m-d H:i:s") . "] ERROR: " . $error);
}

header("Location: ./");
exit;
?>