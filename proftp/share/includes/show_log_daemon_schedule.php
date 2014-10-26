<?php
	session_start();
	require_once("../functions/functions.php");

	if($_SESSION['login']['username']){
		if(isset($_POST['datebegin'])){$datebegin = $_POST['datebegin'];}
		else{$datebegin = roundupMinutes(date("Y:m:d H:i:s", strtotime("-1 day")));}
		if(isset($_POST['dateend'])){$dateend = $_POST['dateend'];}
		else{$dateend = roundupMinutes(date("Y:m:d H:i:s", strtotime("now")));}
		$timebegin = strtotime($datebegin);
		$timeend = strtotime($dateend);
		$timediff = $timeend - $timebegin;
		
		require('../includes/inc_chart_interval.php');

		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);

		//Pages

		$entries = 0;
		$entries_info = 0;
		$entries_error = 0;
		$query_ext = " WHERE created > '".$datebegin."' AND created < '".$dateend."'";
		if(!empty($_POST['filter'])){
			$query_ext .= " AND ".$_POST['filter'];
		}
		else{
			$_POST['filter'] = "";
		}
		$query = "SELECT status,COUNT(*) as count FROM guicmds$query_ext GROUP BY status;";
		//echo "$query<br>";
		$result = $db->query($query);
		if($db->num_rows($result) > 0){
			while($array = $db->fetch_array_assoc($result)){
				$entries += $array['count'];
				if($array['status'] == "info"){
					$entries_info = $array['count'];
				}
				elseif($array['status'] == "error"){
					$entries_error = $array['count'];
				}
			}
		}
		if($entries > $_SESSION['login']['max_list_log_items']){$pages = ceil($entries / $_SESSION['login']['max_list_log_items']);}
		else{$pages = 1;}

		//Graph

		$barwidth = 800;
		$barheight = 400;
		$graphbuild = array();
		$graphlabes = "";
		$graphdata = "";
		$graphdata_info = "";
		$graphdata_error = "";
		$count = 0;
		$query = "SELECT status, DATE(created) AS date, SEC_TO_TIME(TIME_TO_SEC(created) - TIME_TO_SEC(created)%($graphinterval)) AS intervals, COUNT(*) AS count FROM guicmds$query_ext GROUP BY intervals,status ORDER BY date,intervals;";
		//echo "$query<br>";
		$result = $db->query($query);
		$starttime = "";
		if($db->num_rows($result) > 0){
			while($array = $db->fetch_array_assoc($result)){
				$time = strtotime($array['date']." ".$array['intervals']);
				if(!$starttime){$starttime = $time;}
				$graphbuild[intval($time)]['val'.$array['status']] = $array['count'];
			}
		}

		if(!$starttime){$starttime = $timebegin;}
		for($i = $starttime; $i >= $timebegin; $i -= $graphinterval){
			$graphbuild[$i]['time'] = $i;
			if(empty($graphbuild[$i]['valinfo'])){
				$graphbuild[$i]['valinfo'] = 0;
			}
			if(empty($graphbuild[$i]['valerror'])){
				$graphbuild[$i]['valerror'] = 0;
			}
			$graphbuild[$i]['val'] = $graphbuild[$i]['valinfo'] + $graphbuild[$i]['valerror'];
		}
		for($i = $starttime; $i <= $timeend; $i += $graphinterval){
			$graphbuild[$i]['time'] = $i;
			if(empty($graphbuild[$i]['valinfo'])){
				$graphbuild[$i]['valinfo'] = 0;
			}
			if(empty($graphbuild[$i]['valerror'])){
				$graphbuild[$i]['valerror'] = 0;
			}
			$graphbuild[$i]['val'] = $graphbuild[$i]['valinfo'] + $graphbuild[$i]['valerror'];
		}

		ksort($graphbuild);
		foreach($graphbuild as $data){
			if($graphlabes){
				$graphlabes .= ",";
				$graphdata .= ",";
				$graphdata_info .= ",";
				$graphdata_error .= ",";
			}
			if($timediff > 86400 * 2){
				$graphlabes .= '"'.date("Y-m-d", $data['time']).'"';
			}
			else{
				$graphlabes .= '"'.date("Y-m-d H:i:00", $data['time']).'"';
			}
			$graphdata .= $data['val'];
			$graphdata_info .= $data['valinfo'];
			$graphdata_error .= $data['valerror'];
		}

		//Log

		echo "<span class=\"hidden\" id=\"datemanipvals\">";
			$values = array("1 hour","6 hours","12 hours","1 day","3 days","1 week","2 weeks","1 month","3 months","6 months","1 year","2 years","5 years");
			foreach($values as $value){
				echo "<div class=\"dropvalue\" onclick=\"setDropDown('datemanip','$value',1);\">$value</div>";
			}
			if(empty($_POST['datemanip'])){$_POST['datemanip'] = $values[3];}
		echo "
	</span>
	<div class=\"drop\" id=\"drop\"></div>
	<div class=\"block\">
		<br>
		<div align=\"center\">
			<canvas id=\"logbarchart\" width=\"$barwidth\" height=\"$barheight\"></canvas>
			<br>
			<span class=\"infosmall\">Interval: $graphintervalstr</span>
			<br>
			<span id=\"logbarlegend\"></span>
		</div>
		<br>
		<div align=\"center\">
			<form id=\"formfilter\">
				<input class=\"filter\" style=\"width:100px;\" type=\"text\" name=\"datemanip\" id=\"datemanip\" value=\"".$_POST['datemanip']."\" onclick=\"showDropDown(this.id);\" onkeyup=\"alterDateFilter();\" />
				<input class=\"filter\" type=\"text\" name=\"datebegin\" id=\"datebegin\" value=\"$datebegin\" />
				<input class=\"filter\" type=\"text\" name=\"dateend\" id=\"dateend\" value=\"$dateend\" />
				<input class=\"filter\" style=\"width:50%;\" type=\"text\" name=\"filter\" value=\"".$_POST['filter']."\" placeholder=\"Filter (eg. command = 'STOR' AND ip = '127.0.0.1 ' AND NOT file LIKE '%test.txt')\" />
				<span class=\"filter\" id=\"filtergo\" onclick=\"showTab('log','xfer','1','0','');\">GO</span>
			<form>
		</div>
		<br>
";
			showPageIndex($_POST['pagename'],$_POST['tabname'],$_POST['page'],$pages,$_POST['action']);
			echo "
		<br>
		<table align=\"center\" class=\"list\">
";
		if($_POST['page'] && $_POST['page'] > 1){$mysql_page = $_POST['page'] * $_SESSION['login']['max_list_log_items'] - $_SESSION['login']['max_list_log_items'];}
		else{$mysql_page = 0;}
		$query = "SELECT * FROM guicmds$query_ext ORDER BY created DESC LIMIT $mysql_page, ".$_SESSION['login']['max_list_log_items'].";";
		$result = $db->query($query);
		$status = array("Pending","Failed","Completed");
		if($db->num_rows($result) > 0){
			echo "
			<tr>
				<th class=\"list\">Status</th>
				<th class=\"list\">Command</th>
				<th class=\"list\">Params</th>
				<th class=\"list\">Result</th>
				<th class=\"list\">Created by</th>
				<th class=\"list\">Created</th>
				<th class=\"list\">Compledted</th>
			</tr>
";
			$switch_list = 1;
			while($array = $db->fetch_array_assoc($result)){
				$errorcolor = "";
				if($array['status'] == 1){$errorcolor = " style=\"color:red;\"";}
				echo "
			<tr class=\"list$switch_list\"$errorcolor>
				<td class=\"list\">".$status[$array['status']]."</td>
				<td class=\"list\">".$array['command']."</td>
				<td class=\"list\">".$array['params']."</td>
				<td class=\"list\">".$array['result']."</td>
				<td class=\"list\">".$array['created_by']."</td>
				<td class=\"list\">".$array['created']."</td>
				<td class=\"list\">".$array['completed']."</td>
			</tr>
";
				if($switch_list){$switch_list = 0;}
				else{$switch_list = 1;}
			}
			echo "
		</table>
		<div align=\"center\">
			<span class=\"infosmall\">Entries: $entries - Info: $entries_info - Error: $entries_error</span>
		</div>
";
			showPageIndex($_POST['pagename'],$_POST['tabname'],$_POST['page'],$pages,$_POST['action']);
		}
		else{
			echo "<div align=\"center\" class=\"error\">No log entries found</div>";
		}
		echo "
		<br>
	</div>
";
		$db->close();
?>
<script>
	$('#datebegin').datetimepicker({
		lang:'en',
		step:10,
		format:'Y-m-d H:i:00'
	});
	
	$('#dateend').datetimepicker({
		lang:'en',
		step:10,
		format:'Y-m-d H:i:00'
	});

	canvas = document.getElementById("logbarchart");
	canvas.width = window.innerWidth - 150;
	ctx = canvas.getContext("2d");
	var data = {
		labels: [<?php echo $graphlabes; ?>],
		datasets: [
			{
				label: "Error",
				fillColor: "rgba(247,70,74,0.2)",
				strokeColor: "rgba(247,70,74,1)",
				pointColor: "rgba(247,70,74,1)",
				pointStrokeColor: "#fff",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(247,70,74,1)",
				data: [<?php echo $graphdata_error; ?>]
			},
			{
				label: "Info",
				fillColor: "rgba(70,191,189,0.2)",
				strokeColor: "rgba(70,191,189,1)",
				pointColor: "rgba(70,191,189,1)",
				pointStrokeColor: "#fff",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(70,191,189,1)",
				data: [<?php echo $graphdata_info; ?>]
			},
			{
				label: "All",
				fillColor: "rgba(220,220,220,0.2)",
				strokeColor: "rgba(66,66,66,1)",
				pointColor: "rgba(66,66,66,1)",
				pointStrokeColor: "#fff",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(66,66,66,1)",
				data: [<?php echo $graphdata; ?>]
			}
		],
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
