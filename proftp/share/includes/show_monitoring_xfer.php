<?php
	session_start();
	require_once("../functions/functions.php");
	if(isset($_SESSION['login']['username'])){
		if(isset($_POST['datebegin'])){$datebegin = $_POST['datebegin'];}
		else{$datebegin = roundupMinutes(date("Y:m:d H:i:s", strtotime("-1 day")));}
		if(isset($_POST['dateend'])){$dateend = $_POST['dateend'];}
		else{$dateend = roundupMinutes(date("Y:m:d H:i:s", strtotime("now")));}
		$timebegin = strtotime($datebegin);
		$timeend = strtotime($dateend);
		$timediff = $timeend - $timebegin;

		require('../includes/inc_chart_interval.php');

		$xfer_rrd = "../../rrd/xfer.rrd";
		$graphlabels = "";
		$graphdata_in = "";
		$graphdata_out = "";
		$graphdata_xfer = "";
		$graphdata = array();
		if(file_exists($xfer_rrd)){
			$result = rrd_fetch( $xfer_rrd, array( "MAX", "--resolution", $graphinterval, "--start", $timebegin, "--end", $timeend ) );
			foreach($result['data']['bytesIn'] as $key => $val){
				$val = trim($val);
				$val = round($val / 1024);
				$mysqldate = date("Y-m-d H:i:00", $key);
				if($graphlabels){$graphlabels .= ",";}
				if($graphdata_in || $graphdata_in == "0"){$graphdata_in .= ",";}
				$graphlabels .= "'".$mysqldate."'";
				if($val == "NAN"){$graphdata_in .= "0";}
				else{$graphdata_in .= $val;}
				$graphdata[$key]['bytesIn'] = $val;
			}
			foreach($result['data']['bytesOut'] as $key => $val){
				$val = trim($val);
				$val = round($val / 1024);
				$mysqldate = date("Y-m-d H:i:00", $key);
				if($graphdata_out || $graphdata_out == "0"){$graphdata_out .= ",";}
				if($val == "NAN"){$graphdata_out .= "0";}
				else{$graphdata_out .= $val;}
				$graphdata[$key]['bytesOut'] = $val;
			}
			foreach($result['data']['bytesXfer'] as $key => $val){
				$val = trim($val);
				$val = round($val / 1024);
				$mysqldate = date("Y-m-d H:i:00", $key);
				if($graphdata_xfer || $graphdata_xfer == "0"){$graphdata_xfer .= ",";}
				if($val == "NAN"){$graphdata_xfer .= "0";}
				else{$graphdata_xfer .= $val;}
				$graphdata[$key]['bytesXfer'] = $val;
			}
		}
		//echo "$graphdata_in<br>$graphdata_out<br>$graphdata_xfer<br>";
	}
	echo "<span class=\"hidden\" id=\"datemanipvals\">";
	$values = array("1 hour","6 hours","12 hours","1 day","3 days","1 week","2 weeks","1 month","3 months","6 months","1 year","2 years","5 years");
	foreach($values as $value){
		echo "<div class=\"dropvalue\" onclick=\"setDropDown('datemanip','$value',1);\">$value</div>";
	}
	if(empty($_POST['datemanip'])){$_POST['datemanip'] = $values[3];}
	echo "
	</span>
	<div class=\"drop\" id=\"drop\"></div>
	<div class=\"block\" align=\"center\">
		<div align=\"center\">
			<form id=\"formfilter\">
				<input class=\"filter\" style=\"width:100px;\" type=\"text\" name=\"datemanip\" id=\"datemanip\" value=\"".$_POST['datemanip']."\" onclick=\"showDropDown(this.id);\" onkeyup=\"alterDateFilter();\" />
				<input class=\"filter\" type=\"text\" name=\"datebegin\" id=\"datebegin\" value=\"$datebegin\" />
				<input class=\"filter\" type=\"text\" name=\"dateend\" id=\"dateend\" value=\"$dateend\" />
				<span class=\"filter\" id=\"filtergo\" onclick=\"showTab('monitoring','xfer','1','0');\">GO</span>
			<form>
		</div>
		<br>
		<canvas id=\"xferchart\" width=\"600\" height=\"400\"></canvas>
		<br>
		<span class=\"infosmall\">Interval: $graphintervalstr</span>
		<br>
		<span id=\"logbarlegend\"></span>
		<br>
		<table align=\"center\" class=\"detailstitle\">
			<tr>
				<th class=\"detailstitleleft\">Time</th>
				<th class=\"detailstitleleft\">kB in</th>
				<th class=\"detailstitleleft\">kB out</th>
				<th class=\"detailstitleleft\">kB xfer</th>
			</tr>
";
	foreach($graphdata as $key => $val){
		echo "
			<tr>
				<td class=\"detailsval\">".date("Y-m-d H:i:00", $key)."</td>
				<td class=\"detailsval\">".$val['bytesIn']."</td><td>".$val['bytesOut']."</td>
				<td class=\"detailsval\">".$val['bytesXfer']."</td>
			</tr>
";
	}
	echo "
		</table>
	</div>
";
?>
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
				strokeColor: "rgba(66,66,66,1)",
				pointColor: "rgba(66,66,66,1)",
				pointStrokeColor: "#fff",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(66,66,66,1)",
				data: [<?php echo $graphdata_xfer; ?>]
			}
		]
	};
	if(myBarChart){
		myBarChart.clear();
		myBarChart.destroy();
	}
	var myBarChart = new Chart(ctx).Line(data,{
                legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
        });
        $('#logbarlegend').html(myBarChart.generateLegend());
</script>
