<?php
	session_start();
	require_once("../functions/functions.php");

	function roundupMinutes($date){
		$datetime = new DateTime($date);
		$minutes = $datetime->format('i') % 10;
		if($minutes > 0){
			$datetime->modify("+10 minutes");
			$datetime->modify("-".$minutes." minutes");
		}
		return $datetime->format('Y-m-d H:i:00');
	}

	if($_SESSION['login']['username'] || true){
		if(isset($_POST['datebegin'])){
			$datebegin = $_POST['datebegin'];
		}
		else{
			$datebegin = roundupMinutes(date("Y:m:d h:i:s", strtotime("-1 day")));
		}
		if(isset($_POST['dateend'])){
			$dateend = $_POST['dateend'];
		}
		else{
			$dateend = roundupMinutes(date("Y:m:d h:i:s", strtotime("now")));
		}
		$timebegin = strtotime($datebegin);
		$timeend = strtotime($dateend);
		$timediff = $timeend - $timebegin;
		
		require('../classes/mysql.php');
		include('../includes/inc_ftp_codes.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);

		//Pages

		$entries = 0;
		$entries_info = 0;
		$entries_error = 0;
		$query_ext = " WHERE time > '".$datebegin."' AND time < '".$dateend."'";
		if(!empty($_POST['filter'])){
			$query_ext .= " AND ".$_POST['filter'];
		}
		else{
			$_POST['filter'] = "";
		}
		$query = "SELECT errorlevel,COUNT(*) as count FROM xfer$query_ext GROUP BY errorlevel;";
		//echo "$query<br>";
		$result = $db->query($query);
		if($db->num_rows($result) > 0){
			while($array = $db->fetch_array_assoc($result)){
				$entries += $array['count'];
				if($array['errorlevel'] == "info"){
					$entries_info = $array['count'];
				}
				elseif($array['errorlevel'] == "error"){
					$entries_error = $array['count'];
				}
			}
		}
		if($entries > $_SESSION['login']['max_list_log_items']){$pages = ceil($entries / $_SESSION['login']['max_list_log_items']);}
		else{$pages = 1;}

		//Graph

		if($timediff > 29030400){#1 Year
			$graphinterval = 31556926;
			$graphintervalstr = "Years";
		}
		elseif($timediff > 2419200 * 3){#3 Month
			$graphinterval = 2629744;
			$graphintervalstr = "Months";
		}
		elseif($timediff > 604800 * 4){#4 Week
			$graphinterval = 604800;
			$graphintervalstr = "Weeks";
		}
		elseif($timediff > 86400 * 6){#6 Day
			$graphinterval = 86400;
			$graphintervalstr = "Days";
		}
		elseif($timediff > 86400 * 4){#4 Day
			$graphinterval = 14400;
			$graphintervalstr = "4 Hours";
		}
		elseif($timediff > 86400 * 2){#2 Day
			$graphinterval = 7200;
			$graphintervalstr = "2 Hours";
		}
		elseif($timediff > 3600){#1 Hour
			$graphinterval = 3600;
			$graphintervalstr = "Hours";
		}
		elseif($timediff > 60){#1 Minute
			$graphinterval = 60;
			$graphintervalstr = "Minutes";
		}
		else{
			$graphinterval = 1;
			$graphintervalstr = "Seconds";
		}

		$barwidth = 800;
		$barheight = 400;
		$graphbuild = array();
		$graphlabes = "";
		$graphdata = "";
		$graphdata_info = "";
		$graphdata_error = "";
		$count = 0;
		$query = "SELECT errorlevel, DATE(time) AS date, SEC_TO_TIME(TIME_TO_SEC(time) - TIME_TO_SEC(time)%($graphinterval)) AS intervals, COUNT(*) AS count FROM xfer$query_ext GROUP BY intervals,errorlevel ORDER BY date,intervals;";
		//echo "$query<br>";
		$result = $db->query($query);
		$starttime = "";
		if($db->num_rows($result) > 0){
			while($array = $db->fetch_array_assoc($result)){
				$time = strtotime($array['date']." ".$array['intervals']);
				if(!$starttime){$starttime = $time;}
				$graphbuild[intval($time)]['val'.$array['errorlevel']] = $array['count'];
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
		</div>
		<br>
		<div align=\"center\">
			<form id=\"formfilter\">
				<input class=\"filter\" style=\"width:100px;\" type=\"text\" name=\"datemanip\" id=\"datemanip\" value=\"".$_POST['datemanip']."\" onclick=\"showDropDown(this.id);\" onkeyup=\"alterDateFilter();\" />
				<input class=\"filter\" type=\"text\" name=\"datebegin\" id=\"datebegin\" value=\"$datebegin\" />
				<input class=\"filter\" type=\"text\" name=\"dateend\" id=\"dateend\" value=\"$dateend\" />
				<input class=\"filter\" style=\"width:50%;\" type=\"text\" name=\"filter\" value=\"".$_POST['filter']."\" placeholder=\"Filter (eg. command = 'STOR' AND ip = '127.0.0.1 ' AND NOT file LIKE '%test.txt')\" />
				<span class=\"filter\" id=\"filtergo\" onclick=\"showTab('log','xfer','1','0');\">GO</span>
			<form>
		</div>
		<br>
";
			showPageIndex($_POST['pagename'],$_POST['tabname'],$_POST['page'],$pages,$_POST['action']);
			echo "
		<br>
		<table align=\"center\" class=\"detailstitle\">
";
		if($_POST['page'] && $_POST['page'] > 1){$mysql_page = $_POST['page'] * $_SESSION['login']['max_list_log_items'] - $_SESSION['login']['max_list_log_items'];}
		else{$mysql_page = 0;}
		$query = "SELECT * FROM xfer$query_ext ORDER BY time DESC LIMIT $mysql_page, ".$_SESSION['login']['max_list_log_items'].";";
		$result = $db->query($query);
		if($db->num_rows($result) > 0){
			echo "
			<tr>
				<th class=\"detailstitleleft\">Errorlevel</th>
				<th class=\"detailstitleleft\">Userid</th>
				<th class=\"detailstitleleft\">Command</th>
				<th class=\"detailstitleleft\">Response</th>
				<th class=\"detailstitleleft\">Filename</th>
				<th class=\"detailstitleleft\">Size</th>
				<th class=\"detailstitleleft\">Hostname</th>
				<th class=\"detailstitleleft\">IP</th>
				<th class=\"detailstitleleft\">Timespent</th>
				<th class=\"detailstitleleft\">Time</th>
			</tr>
";
			$switch_list = 1;
			while($array = $db->fetch_array_assoc($result)){
				$errorcolor = "";
				if($array['errorlevel'] == "error"){$errorcolor = " style=\"color:red;\"";}
				$returncodestr = "";
				if($array['response'] && $array['response'] != "-"){
					$returncodestr = $ftpcode[$array['response']];
				}
				echo "
			<tr class=\"list$switch_list\"$errorcolor>
				<td class=\"detailsval\">".$array['errorlevel']."</td>
				<td class=\"detailsval\">".$array['userid']."</td>
				<td class=\"detailsval\">".$array['command']."</td>
				<td class=\"detailsval\" title=\"$returncodestr\">".$array['response']."</td>
				<td class=\"detailsval\">".$array['filename']."</td>
				<td class=\"detailsval\">".$array['size']."</td>
				<td class=\"detailsval\">".$array['hostname']."</td>
				<td class=\"detailsval\">".$array['ip']."</td>
				<td class=\"detailsval\">".$array['timespent']."</td>
				<td class=\"detailsval\">".$array['time']."</td>
				<td class=\"detailsval\"></td>
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
		<br>
";
			showPageIndex($_POST['pagename'],$_POST['tabname'],$_POST['page'],$pages,$_POST['action']);
			echo "
		<br>
	</div>
";
			if($_POST['action'] == 1){
				echo "<div align=\"center\"><a href=\"#\" onclick=\"showPrevPage();return false;\">Close</div>";
			}
		}
		$db->close();
	}
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
				strokeColor: "rgba(220,220,220,1)",
				pointColor: "rgba(220,220,220,1)",
				pointStrokeColor: "#fff",
				pointHighlightFill: "#fff",
				pointHighlightStroke: "rgba(220,220,220,1)",
				data: [<?php echo $graphdata; ?>]
			}
		]
	};
	if(myBarChart){
		myBarChart.clear();
		myBarChart.destroy();
	}
	var myBarChart = new Chart(ctx).Line(data);
</script>
