<?php
	session_start();
	require_once("../functions/functions.php");

	function roundupMinutesFromDatetime(\Datetime $date, $minuteOff = 10){
		$string = sprintf(
			"%d minutes %d seconds", 
			$date->format("i") % $minuteOff, 
			$date->format("s")
		);
		return  $date->sub(\DateInterval::createFromDateString($string));
	}

	if($_SESSION['login']['username'] || true){
		if(isset($_POST['datebegin'])){
			$datebegin = $_POST['datebegin'];
		}
		else{
			$datebegin = new DateTime(date("Y:m:d h:i:s", strtotime("-1 day")));
			$datebegin = roundupMinutesFromDatetime($datebegin);
			$datebegin = $datebegin->format('Y-m-d H:i:s');
		}
		if(isset($_POST['dateend'])){
			$dateend = $_POST['dateend'];
		}
		else{
			$dateend = new DateTime(date("Y:m:d h:i:s", strtotime("now")));
			$dateend = roundupMinutesFromDatetime($dateend);
			$dateend = $dateend->format('Y-m-d H:i:s');
		}
		$timebegin = strtotime($datebegin);
		$timeend = strtotime($dateend);
		$timediff = $timeend - $timebegin;
		
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);

		//Pages

		if($_POST['page'] && $_POST['page'] > 1){$mysql_page = $_POST['page'] * $_SESSION['login']['max_list_log_items'] - $_SESSION['login']['max_list_log_items'];}
		else{$mysql_page = 0;}
		$entries = 0;
		$query_ext = " WHERE time > '".$datebegin."' AND time < '".$dateend."'";
		if(!empty($_POST['filter'])){
			$query_ext .= " AND ".$_POST['filter'];
		}
		else{
			$_POST['filter'] = "";
		}
		$query = "SELECT COUNT(*) as count FROM xfer$query_ext;";
		//echo "$query<br>";
		$result = $db->query($query);
		if($db->num_rows($result) > 0){
			while($array = $db->fetch_array_assoc($result)){
				$entries = $array['count'];
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
		elseif($timediff > 86400 * 4){#4 Day
			$graphinterval = 86400;
			$graphintervalstr = "Days";
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
		$barheight = 300;
		$graphbuild = array();
		$graphlabes = "";
		$graphdata = "";
		$count = 0;
		$query = "SELECT DATE(time) AS date, SEC_TO_TIME(TIME_TO_SEC(time) - TIME_TO_SEC(time)%($graphinterval)) AS intervals, COUNT(*) AS count FROM xfer$query_ext GROUP BY intervals ORDER BY intervals DESC";
		$result = $db->query($query);
		$starttime = "";
		if($db->num_rows($result) > 0){
			while($array = $db->fetch_array_assoc($result)){
				$time = strtotime($array['date']." ".$array['intervals']);
				if(!$starttime){$starttime = $time;}
				$graphbuild[intval($time)] = $array['count'];
			}
		}
		if(!$starttime){$starttime = $timebegin;}
		for($i = $starttime; $i >= $timebegin; $i -= $graphinterval){
			if(!isset($graphbuild[$i])){
				$graphbuild[$i] = 0;
			}
		}
		for($i = $starttime; $i <= $timeend; $i += $graphinterval){
			if(!isset($graphbuild[$i])){
				$graphbuild[$i] = 0;
			}
		}

		foreach($graphbuild as $label => $data){
			if($graphlabes){
				$graphlabes .= ",";
				$graphdata .= ",";
			}
			$graphlabes .= '"'.date("Y-m-d H:i:00", $label).'"';
			$graphdata .= $data;
		}

		//Log

		$query = "SELECT * FROM xfer$query_ext ORDER BY time DESC LIMIT $mysql_page, ".$_SESSION['login']['max_list_log_items'].";";
		$result = $db->query($query);
		echo "
	<div class=\"block\">
		<br>
		<div align=\"center\">
			<form id=\"formfilter\">
				<input class=\"filter\" type=\"text\" name=\"datebegin\" id=\"datebegin\" value=\"$datebegin\" />
				<input class=\"filter\" type=\"text\" name=\"dateend\" id=\"dateend\" value=\"$dateend\" />
				<input class=\"filter\" style=\"width:50%;\" type=\"text\" name=\"filter\" value=\"".$_POST['filter']."\" placeholder=\"Filter (eg. command = 'STOR' AND ip = '127.0.0.1 ' AND NOT file LIKE '%test.txt')\" />
				<span class=\"filterbut\" onclick=\"showTab('log','xfer','1','0');\">GO</span>
			<form>
		</div>
		<br>
		<div align=\"center\">
			<canvas id=\"logbarchart\" width=\"$barwidth\" height=\"$barheight\"></canvas>
			<br>
			<span class=\"infosmall\">Interval: $graphintervalstr</span>
		</div>
		<br>
";
			showPageIndex($_POST['pagename'],$_POST['tabname'],$_POST['page'],$pages,$_POST['action']);
			echo "
		<br>
		<table align=\"center\" class=\"detailstitle\">
";
		if($db->num_rows($result) > 0){
			echo "
			<tr>
				<th class=\"detailstitleleft\">Errorlevel</th>
				<th class=\"detailstitleleft\">Userid</th>
				<th class=\"detailstitleleft\">Command</th>
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
				echo "
			<tr class=\"list$switch_list\"$errorcolor>
				<td class=\"detailsval\">".$array['errorlevel']."</td>
				<td class=\"detailsval\">".$array['userid']."</td>
				<td class=\"detailsval\">".$array['command']."</td>
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
	canvas.width = window.innerWidth - 200;
	ctx = canvas.getContext("2d");
	var data = {
		labels: [<?php echo $graphlabes; ?>],
		datasets: [
		{
			label: "My Second dataset",
			fillColor: "rgba(151,187,205,0.5)",
			strokeColor: "rgba(151,187,205,0.8)",
			highlightFill: "rgba(151,187,205,0.75)",
			highlightStroke: "rgba(151,187,205,1)",
			data: [<?php echo $graphdata; ?>]
		}
		]
	};
	var myBarChart = new Chart(ctx).Bar(data);
</script>
