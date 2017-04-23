$(init);

function init()
{
	initChart1();
	initChart2();

	$('#check-collection').change(function() {
		$('#charts > div').toggleClass('hidden');
	});

	//initShitstorm();
}

function setHue()
{
	var color = (Math.floor(Math.random() * 65000) + 1);

	$.ajax({
		url: "http://172.19.2.59/api/CG1-ThGZYP4VvLrOHD5cg7CYTY-sMVfHKJ2CuEet/lights/1/state",
		dataType: "json",
		"type": "PUT",
		"data": '{"transitiontime":0,"hue": ' + color + ', "bri": 254, "sat": 254}'
	});
	$.ajax({
		url: "http://172.19.2.59/api/CG1-ThGZYP4VvLrOHD5cg7CYTY-sMVfHKJ2CuEet/lights/2/state",
		dataType: "json",
		"type": "PUT",
		"data": '{"transitiontime":0,"hue": ' + color + ', "bri": 254, "sat": 254}'
	});
	$.ajax({
		url: "http://172.19.2.59/api/CG1-ThGZYP4VvLrOHD5cg7CYTY-sMVfHKJ2CuEet/lights/3/state",
		dataType: "json",
		"type": "PUT",
		"data": '{"transitiontime":0,"hue": ' + color + ', "bri": 254, "sat": 254}'
	});
	$.ajax({
		url: "http://172.19.2.59/api/CG1-ThGZYP4VvLrOHD5cg7CYTY-sMVfHKJ2CuEet/lights/4/state",
		dataType: "json",
		"type": "PUT",
		"data": '{"transitiontime":0,"hue": ' + color + ', "bri": 254, "sat": 254}'
	});
	$.ajax({
		url: "http://172.19.2.59/api/CG1-ThGZYP4VvLrOHD5cg7CYTY-sMVfHKJ2CuEet/lights/5/state",
		dataType: "json",
		"type": "PUT",
		"data": '{"transitiontime":0,"hue": ' + color + ', "bri": 254, "sat": 254}'
	});
	$.ajax({
		url: "http://172.19.2.59/api/CG1-ThGZYP4VvLrOHD5cg7CYTY-sMVfHKJ2CuEet/lights/6/state",
		dataType: "json",
		"type": "PUT",
		"data": '{"transitiontime":0,"hue": ' + color + ', "bri": 254, "sat": 254}'
	});
	$.ajax({
		url: "http://172.19.2.59/api/CG1-ThGZYP4VvLrOHD5cg7CYTY-sMVfHKJ2CuEet/lights/7/state",
		dataType: "json",
		"type": "PUT",
		"data": '{"transitiontime":0,"hue": ' + color + ', "bri": 254, "sat": 254}'
	});
	$.ajax({
		url: "http://172.19.2.59/api/CG1-ThGZYP4VvLrOHD5cg7CYTY-sMVfHKJ2CuEet/lights/8/state",
		dataType: "json",
		"type": "PUT",
		"data": '{"transitiontime":0,"hue": ' + color + ', "bri": 254, "sat": 254}'
	});
	$.ajax({
		url: "http://172.19.2.59/api/CG1-ThGZYP4VvLrOHD5cg7CYTY-sMVfHKJ2CuEet/lights/9/state",
		dataType: "json",
		"type": "PUT",
		"data": '{"transitiontime":0,"hue": ' + color + ', "bri": 254, "sat": 254}'
	});

	console.log(color);
}

function initShitstorm()
{
	setInterval(function () {setHue() }, 50);
}

function initChart1()
{
	Highcharts.chart('muhChart1', {
		chart: {
			type: 'spline',
			width: 847
		},
		title: {
			text: 'Energy Consumption'
		},
		credits: {
			enabled: false
		},
		series: chartall,
		xAxis: {
			type: 'datetime'
		},
		yAxis: {
			text: 'Consumption'
		},
		plotOptions: {
			series: {
				pointStart: Date.UTC(2017, 3, 21, 13, 10),
				pointInterval: 600 * 1000 // ten minutes
			}
		},
	});
}

function initChart2()
{
	Highcharts.chart('muhChart2', {
		chart: {
			type: 'spline',
			width: 847
		},
		title: {
			text: 'Energy Consumption'
		},
		credits: {
			enabled: false
		},
		series: [{
			name: "All lights",
			data: charttotal
		}],
		xAxis: {
			type: 'datetime'
		},
		yAxis: {
			text: 'Consumption'
		},
		plotOptions: {
			series: {
				pointStart: Date.UTC(2017, 3, 21, 13, 10),
				pointInterval: 600 * 1000 // ten minutes
			}
		},
	});
}