<?php
include_once 'includes/db.php';

$get_api_details = mysqli_query($db, "SELECT hue_ip, hue_username FROM users WHERE `id` = 1");
if (!$get_api_details)
{
	die(mysqli_error($db));
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
} else if ($api_username == "")
{
	$error = "No Hue Username";
}

if ($error == "") {
	$api_url = "http://" . $api_ip . "/api/" . $api_username . "/";
	$api_call = file_get_contents($api_url . "lights");
	if (!$api_call)
	{
		$error = "Can't reach Bridge API";
	} else
	{
		$api_response = json_decode($api_call, true);
	}
}
?>
<html>
	<?php include_once 'includes/header.php'; ?>
	<body>
		<?php include_once 'includes/navigation.php'; ?>
		<div class="container">
			<h2>Live status</h2>
			<div class="row">
			<?php
			if ($error != "")
			{
				echo $error;
			}
			else
			{
				$count = 0;
				$counton = 0;
				$countoff = 0;
				$countfails = 0;

				foreach($api_response as $key => $item)
				{
					$count++;

					$light = $item['state'];
					if ($light['on'] == true && $light['reachable'] == true) {
						$class = "enabled";
						$counton++;
					} else if ($light['reachable'] == true) {
						$class = "disabled";
						$countoff++;
					} else {
						$class = "connection";
						$countfails++;
					}
					?>
					<div class='col-sm-3 col-xs-4'>
						<div class="hue <?= $class ?>">
							<div title="Hue: <?= $light['hue'] . "\n" . "Saturation: " . $light['sat'] . "\nBrightness: " . round($light['bri']) . "\nhsl(" . round($light['hue'] / 182) . ", " . round($light['sat'] / 2.54) . "%, " . round($light['bri'] / 5.08) ?>%)" class="colorbox" style="background-color: hsl(<?= round($light['hue'] / 182) . ", ". round($light['sat'] / 2.54) . "%, 50%)" ?>">
								<?php if ($light['reachable'] == true && $light['on'] == true && $light['bri'] < 230) { ?>
								<div class="brightness">
									<div class="bar" style="height: <?= 100 - ($light['bri'] / 2.54) ?>%"></div>
								</div>
								<?php } ?>
							</div>
							<div class="info">
							<h3 title="ID: <?= $item['uniqueid'] ?>"><?= $item['name'] ?></h3>
							<p>
								<?php if ($light['reachable'] == true) { ?>
									<br><i class="glyphicon glyphicon-ok" style="color:darkgreen;"></i> Reached
								<?php } else { ?>
									<br><i class="glyphicon glyphicon-remove" style="color:red;"></i> Connection lost
								<?php } ?>
								<?php if ($light['reachable'] == true && $light['on'] == "ON") { ?>
									<br><i class="glyphicon glyphicon-ok" style="color:darkgreen;"></i> Turned On
									<br><i class="glyphicon glyphicon-certificate" style="color:gray;"></i> Brightness: <?php if (round($light['bri'] / 2.54) == 0) { echo "1"; } else { echo round($light['bri'] / 2.54); } ?>%
								<?php } else { ?>
									<br><i class="glyphicon glyphicon-remove" style="color:red;"></i> Turned Off
									<br><i class="glyphicon glyphicon-certificate"></i> Brightness: 0%
								<?php } ?>
							</p>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
			<hr>
			<div class="row">
				<div class="col-sm-3">
					<div class="lead">
						<?= "<span class='label label-primary'>" . $count . "</span> lamps checked" ?>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="lead">
						<?= "<span class='label label-success'>" . $counton . "</span> lamps were turned on" ?>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="lead">
						<?= "<span class='label label-default'>" . $countoff . "</span> lamps were turned off" ?>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="lead">
						<?= "<span class='label label-danger'>" . $countfails . "</span> lamps couldn't be reached" ?>
					</div>
				</div>
			<?php } ?>
			</div>
			<hr>
			<div class="row"></div>
			<h2>Activity<small class="pull-right">Up to 10 minutes delay. <a href="croncheck.php"><i class="glyphicon glyphicon-refresh"></i> Refresh now</a></small></h2>
			<div class="row">
				<div class="col-sm-9" id="charts">
					<div id="muhChart2" class="hidden" width="847" height="400"></div>
					<div id="muhChart1" width="847" height="400"></div>
				</div>
				<div class="col-sm-3">
					<label for="check-graph">Type</label>
					<input id="check-graph" data-style="custom" type="checkbox" data-width="262" checked data-toggle="toggle" data-on="Consumption" data-off="Cost" data-onstyle="warning" data-offstyle="danger">
					<hr>
					<label for="check-other">Other light types</label>
					<input id="check-other" data-style="custom" type="checkbox" data-width="262" checked data-toggle="toggle" data-on="Shown" data-off="Hidden" data-onstyle="success" data-offstyle="danger">
					<hr>
					<label for="check-collection">Lights</label>
					<input id="check-collection" data-style="custom" type="checkbox" data-width="262" checked data-toggle="toggle" data-off="Total" data-on="Individual" data-offstyle="primary" data-onstyle="info">
				</div>
			</div>
		</div>
		<?php include_once 'includes/scripts.php'; ?>
		<script type="text/javascript">
			<?php
				$gettotalentries = mysqli_query($db, "SELECT SUM(`brightness`), `datetime` FROM `hue_entries` WHERE `datetime` BETWEEN (NOW() - INTERVAL 2 DAY) AND NOW() GROUP BY `datetime` LIMIT 300;");
				while ($entry = mysqli_fetch_assoc($gettotalentries))
				{
					$dates[] = $entry['datetime'];
					$totalentries[] = (($entry['SUM(`brightness`)'] + 0) / 2.54);
				}
				echo "var charttotal = ";
				print_r(json_encode($totalentries));
				echo ";";


				$getalllights = mysqli_query($db, "SELECT DISTINCT `hue_id`,`name`,`hue`,`saturation`,`brightness`  FROM `hue_entries` WHERE `datetime` BETWEEN (NOW() - INTERVAL 2 DAY) AND NOW() ORDER BY `id` DESC LIMIT 8;");
				$i = 0;
				while ($light = mysqli_fetch_assoc($getalllights))
				{
					$allentries[$i]['name'] = "" . $light['name'];
					$allentries[$i]['color'] = "hsl(" . round($light['hue'] / 182) . ", " . round($light['saturation'] / 2.54) . "%, " . round($light['brightness'] / 5.08) . "%)";

					$getallentries = mysqli_query( $db, "SELECT `hue`,`saturation`,`brightness` FROM `hue_entries` WHERE `hue_id` = '" . $light['hue_id'] . "' AND `datetime` BETWEEN (NOW() - INTERVAL 2 DAY) AND NOW() LIMIT 300;" );
					while($entry = mysqli_fetch_assoc($getallentries) )
					{
						$allentries[$i]['data'][] = ($entry['brightness'] + 0) / 2.54;
						$allentries[$i]['color'] = "hsl(" . round($light['hue'] / 182) . ", " . round($light['saturation'] / 2.54) . "%, " . round($light['brightness'] / 5.08) . "%)";
					}
					$i++;
				}

				echo "var chartall = ";
				print_r(json_encode($allentries));
				echo ";";
			?>

			console.log(chartall);
		</script>
	</body>
</html>