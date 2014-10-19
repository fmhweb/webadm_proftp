<!DOCTYPE html>
<html>
	<head>
		<script src="js/Chart.js"></script>
		<script src="js/monitoring.js"></script>
	</head>
	<body>
		<canvas id="myChart" width="400" height="400"></canvas>
		<script>
			var ctx = document.getElementById("myChart").getContext("2d");
			var myLineChart = new Chart(ctx).Line(data);
		</script>
	</body>
</html>
