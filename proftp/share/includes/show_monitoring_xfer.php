<?php
	session_start();
	require_once("../functions/functions.php");
	if(isset($_SESSION['login']['username'])){
		$xfer_rrd = "../../rrd/xfer.rrd";
		$graphlabels = "";
		$graphdata_in = "";
		$graphdata_out = "";
		$graphdata_xfer = "";
		if(file_exists($xfer_rrd)){
			$result = rrd_fetch( $xfer_rrd, array( "AVERAGE", "--resolution", "3600", "--start", "-1d" ) );
			foreach($result['data']['bytesIn'] as $key => $val){
				$val = trim($val);
				if($graphlabels){$graphlabels .= ",";}
				if($graphdata_in || $graphdata_in == "0"){$graphdata_in .= ",";}
				$graphlabels .= "'".date("Y-m-d H:i:00", $key)."'";
				if($val == "NAN"){$graphdata_in .= "0";}
				else{$graphdata_in .= round($val / 1024);}
			}
			foreach($result['data']['bytesOut'] as $val){
				$val = trim($val);
				if($graphdata_out || $graphdata_out == "0"){$graphdata_out .= ",";}
				if($val == "NAN"){$graphdata_out .= "0";}
				else{$graphdata_out .= round($val / 1024);}
			}
			foreach($result['data']['bytesXfer'] as $val){
				$val = trim($val);
				if($graphdata_xfer || $graphdata_xfer == "0"){$graphdata_xfer .= ",";}
				if($val == "NAN"){$graphdata_xfer .= "0";}
				else{$graphdata_xfer .= round($val / 1024);}
			}
		}
		//echo "$graphdata_in<br>$graphdata_out<br>$graphdata_xfer<br>";
	}
?>
<div class="block" align="center">
	<canvas id="xferchart" width="600" height="400"></canvas>
</div>
<script>
	canvas = document.getElementById("xferchart");
	canvas.width = window.innerWidth - 100;
	ctx = canvas.getContext("2d");
	var data = {
		labels: [<?php echo $graphlabels; ?>],
		datasets: [
			{
				label: "Out",
				fillColor: "rgba(247,70,74,0.2)",
				strokeColor: "rgba(247,70,74,1)",
				pointColor: "rgba(247,70,74,1)",
				pointStrokeColor: "#fff",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(247,70,74,1)",
				data: [<?php echo $graphdata_out; ?>]
			},
			{
				label: "In",
				fillColor: "rgba(70,191,189,0.2)",
				strokeColor: "rgba(70,191,189,1)",
				pointColor: "rgba(70,191,189,1)",
				pointStrokeColor: "#fff",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(70,191,189,1)",
				data: [<?php echo $graphdata_in; ?>]
			},
			{
				label: "Xfer",
				fillColor: "rgba(220,220,220,0.2)",
				strokeColor: "rgba(220,220,220,1)",
				pointColor: "rgba(220,220,220,1)",
				pointStrokeColor: "#fff",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(220,220,220,1)",
				data: [<?php echo $graphdata_xfer; ?>]
			}
		]
	};
	if(myBarChart){
		myBarChart.clear();
		myBarChart.destroy();
	}
	var myBarChart = new Chart(ctx).Line(data);
</script>
