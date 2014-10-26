<?php
	session_start();
	require_once("../functions/functions.php");
	if(isset($_SESSION['login']['username'])){
		if(empty($_POST['user'])){$_POST['user'] = "all";}
		if(isset($_POST['datebegin'])){$datebegin = $_POST['datebegin'];}
		else{$datebegin = roundupMinutes(date("Y:m:d H:i:s", strtotime("-1 day")));}
		if(isset($_POST['dateend'])){$dateend = $_POST['dateend'];}
		else{$dateend = roundupMinutes(date("Y:m:d H:i:s", strtotime("now")));}
		$timebegin = strtotime($datebegin);
		$timeend = strtotime($dateend);
		$timediff = $timeend - $timebegin;

		require('../includes/inc_chart_interval.php');

		if(!empty($_POST['user'])){
			$xfer_rrd = "../../rrd/xfer_".$_POST['user'].".rrd";
		}
		else{
			$xfer_rrd = "../../rrd/xfer.rrd";
		}
		$graphlabels = "";
		$graphdata_in = "";
		$graphdata_out = "";
		$graphdata_xfer = "";
		$graphdata = array();
		if(file_exists($xfer_rrd)){
			$val_in = 0;
			$val_out = 0;
			$val_xfer = 0;
			$steps = 1;
			$result = rrd_fetch( $xfer_rrd, array( "AVERAGE", "--resolution", "300", "--start", $timebegin, "--end", $timeend ) );
			//echo "AVERAGE --resolution 60 --start $timebegin --end $timeend<br>Resolution: $graphinterval<br>";
			end($result['data']['bytesIn']);
			$timeend = key($result['data']['bytesIn']);
			reset($result['data']['bytesIn']);
			$timebegin = key($result['data']['bytesIn']);
			$timenext = $timebegin + $graphinterval;
			foreach($result['data']['bytesIn'] as $key => $val){
				$in = trim($result['data']['bytesIn'][$key]);
				$out = trim($result['data']['bytesOut'][$key]);
				$xfer = trim($result['data']['bytesXfer'][$key]);
				if($in != "NAN"){
					$val_in += ($in / 1024);
					$val_out += $out / 1024;
					$val_xfer += $xfer / 1024;
				}
				$steps++;
				if($key > $timenext || $key == $timeend){
					$val_in = round($val_in / $steps);
					$val_out = round($val_out / $steps);
					$val_xfer = round($val_xfer / $steps);
					if($graphlabels){$graphlabels .= ",";}
					$graphlabels .= "'".date("Y-m-d H:i:00", ($timenext - $graphinterval))."'";

					if($graphdata_in || $graphdata_in == "0"){
						$graphdata_in .= ",";
						$graphdata_out .= ",";
						$graphdata_xfer .= ",";
					}
					$graphdata_in .= $val_in;
					$graphdata_out .= $val_out;
					$graphdata_xfer.= $val_xfer;
					$graphdata[$key]['bytesIn'] = $val_in;
					$graphdata[$key]['bytesOut'] = $val_out;
					$graphdata[$key]['bytesXfer'] = $val_xfer;
					$val_in = 0;
					$val_out = 0;
					$val_xfer = 0;
					$steps = 1;
					$timenext += $graphinterval;
				}
			}
		}
		//echo "$graphlabels<br>$graphdata_in<br>$graphdata_out<br>$graphdata_xfer<br>";
		echo "<span class=\"hidden\" id=\"datemanipvals\">";
		$values = array("1 hour","6 hours","12 hours","1 day","3 days","1 week","2 weeks","1 month","3 months","6 months","1 year","2 years","5 years");
		foreach($values as $value){
			echo "<div class=\"dropvalue\" onclick=\"setDropDown('datemanip','$value',1);\">$value</div>";
		}
		if(empty($_POST['datemanip'])){$_POST['datemanip'] = $values[3];}
		echo "</span><span class=\"hidden\" id=\"uservals\">";
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		$query = "SELECT userid FROM users ORDER BY userid;";
		//echo "$query<br>";
		$result = $db->query($query);
		echo "<div class=\"dropvalue\" onclick=\"setDropDown('user','all',1);\">all</div>";
		if($db->num_rows($result) > 0){
			while($array = $db->fetch_array_assoc($result)){
				echo "<div class=\"dropvalue\" onclick=\"setDropDown('user','".$array['userid']."',1);\">".$array['userid']."</div>";
			}
		}
		$db->close();
		echo "
	</span>
	<div class=\"drop\" id=\"drop\"></div>
	<div class=\"block\" align=\"center\">
		<br>
		<div align=\"center\">
			<form id=\"formfilter\">
				<input class=\"filter\" style=\"width:100px;\" type=\"text\" name=\"datemanip\" id=\"datemanip\" value=\"".$_POST['datemanip']."\" onclick=\"showDropDown(this.id);\" onkeyup=\"alterDateFilter();\" />
				<input class=\"filter\" type=\"text\" name=\"datebegin\" id=\"datebegin\" value=\"$datebegin\" />
				<input class=\"filter\" type=\"text\" name=\"dateend\" id=\"dateend\" value=\"$dateend\" />
				<input class=\"filter\" type=\"text\" name=\"user\" id=\"user\" value=\"".$_POST['user']."\" onclick=\"showDropDown(this.id);\" />
				<span class=\"filter\" id=\"filtergo\" onclick=\"showTab('monitoring','xfer','1','0','');\">GO</span>
			<form>
		</div>
		<br>
		<canvas id=\"xferchart\" width=\"600\" height=\"400\"></canvas>
		<br>
		<span class=\"infosmall\">Interval: $graphintervalstr</span>
		<br>
		<span id=\"logbarlegend\"></span>
		<br>
		<table align=\"center\" class=\"list\">
			<tr>
				<th class=\"list\">Time</th>
				<th class=\"list\">kB in</th>
				<th class=\"list\">kB out</th>
				<th class=\"list\">kB xfer</th>
			</tr>
";
		$switch_list = 1;
		foreach($graphdata as $key => $val){
			echo "
			<tr class=\"list$switch_list\">
				<td class=\"list\">".date("Y-m-d H:i:00", $key)."</td>
				<td class=\"list\">".$val['bytesIn']."</td>
				<td class=\"list\">".$val['bytesOut']."</td>
				<td class=\"list\">".$val['bytesXfer']."</td>
			</tr>
";
			if($switch_list){$switch_list = 0;}
			else{$switch_list = 1;}
		}
		echo "
		</table>
		<br>
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
<?php
	}
?>
