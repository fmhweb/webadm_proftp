<?php
	session_start();
	require_once("../functions/functions.php");

	if(isset($_SESSION['login']['username'])){
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);

		if($_POST['page'] && $_POST['page'] > 1){$mysql_page = $_POST['page'] * $_SESSION['login']['max_list_items'] - $_SESSION['login']['max_list_items'];}
		else{$mysql_page = 0;}

		$pages = 0;
		$query_ext = "";
		if(!empty($_POST['filter'])){
			$query_ext = " WHERE userid LIKE '%".$_POST['filter']."%'";
		}
		else{
			$_POST['filter'] = "";
		}
		$query = "SELECT COUNT(*) as count FROM users$query_ext;";
		$result = $db->query($query);
		if($db->num_rows($result) > 0){
			$array = $db->fetch_array_assoc($result);
			$pages = $array['count'];
		}
		if($pages > $_SESSION['login']['max_list_items']){$pages = ceil($pages / $_SESSION['login']['max_list_items']);}
		else{$pages = 1;}

		$query = "SELECT * FROM users$query_ext ORDER BY userid LIMIT $mysql_page, ".$_SESSION['login']['max_list_items'].";";
		$result = $db->query($query);
		echo "
	<div class=\"block\">
		<br>
		<div align=\"center\">
			<form id=\"formfilter\" onsubmit=\"return false;\">
				<input class=\"filter\" style=\"width:50%;\" type=\"text\" name=\"filter\" value=\"".$_POST['filter']."\" onkeyup=\"evalFilter(event,'user','user','1','0','".$_POST['params']."');\" placeholder=\"Filter (eg. userid)\" />
				<span class=\"filter\" onclick=\"showTab('user','user','1','0','');\">GO</span>
			<form>
		</div>
		<br>
		<div align=\"center\" id=\"resultremove\"></div>
";
			showPageIndex($_POST['pagename'],$_POST['tabname'],$_POST['page'],$pages,$_POST['action']);
			echo "
		<br>
";
		if($_POST['action'] == 0){
		echo "
		<div class=\"options\">
			<a href=\"#\" onclick=\"showDetails('0','user','');return false;\">
				<img src=\"images/user.png\" />
				Add user
			</a>
		</div>
";
		}
		echo "
		<table class=\"list\" align=\"center\">
			<tr>
				<th class=\"list\">Userid</th>
				<th class=\"list\">Surname</th>
				<th class=\"list\">Firstname</th>
				<th class=\"list\">Company</th>
";
		if($_POST['action'] == 0){
		echo "
				<th class=\"list\">Quota</th>
				<th class=\"list\">Keys</th>
				<th class=\"list\"></th>
";
		}
		echo "</tr>";
		if($db->num_rows($result) > 0){
			$switch_list = 1;
			while($array = $db->fetch_array_assoc($result)){
				if($_POST['action'] == 1){
					echo "<tr class=\"list$switch_list\" onclick=\"addAcl(0,'".$array['userid']."','1','user','".$_POST['params']."');\">";
				}
				elseif($_POST['action'] == 2){
					echo "<tr class=\"list$switch_list\" onclick=\"addGroupMember('".$_POST['params']."','".$array['userid']."',0,0);\">";
				}
				else{
					echo "<tr class=\"list$switch_list\" onclick=\"showDetails('".$array['userid']."','user','');\">";
				}
				echo "
				<td class=\"list\">".$array['userid']."</td>
				<td class=\"list\">".$array['surname']."</td>
				<td class=\"list\">".$array['firstname']."</td>
				<td class=\"list\">".$array['company']."</td>
";
				if($_POST['action'] == 0){
				$query = "SELECT * FROM quotalimits a LEFT JOIN quotatallies b ON a.nameid = b.nameid WHERE a.nameid = '".$array['userid']."';";
				$result2 = $db->query($query);
				$quota = 0;
				if($db->num_rows($result2) > 0){
					while($array2 = $db->fetch_array_assoc($result2)){
						$quota = round((100 / $array2['bytes_in_avail']) * $array2['bytes_in_used'],2);
						if(round((100 / $array2['bytes_out_avail']) * $array2['bytes_out_used'],2) > $quota){$quota = round((100 / $array2['bytes_out_avail']) * $array2['bytes_out_used'],2);}
						if(round((100 / $array2['bytes_xfer_avail']) * $array2['bytes_xfer_used'],2) > $quota){$quota = round((100 / $array2['bytes_xfer_avail']) * $array2['bytes_xfer_used'],2);}
						if(round((100 / $array2['files_in_avail']) * $array2['files_in_used'],2) > $quota){$quota = round((100 / $array2['files_in_avail']) * $array2['files_in_used'],2);}
						if(round((100 / $array2['files_out_avail']) * $array2['files_out_used'],2) > $quota){$quota = round((100 / $array2['files_out_avail']) * $array2['files_out_used'],2);}
                				if(round((100 / $array2['files_xfer_avail']) * $array2['files_xfer_used'],2) > $quota){$quota = round((100 / $array2['files_xfer_avail']) * $array2['files_xfer_used'],2);}
					}
				}
				echo "
				<td class=\"list\">$quota%</td>
				<td class=\"list\"></td>
				<td class=\"list\" style=\"text-align:right;\">
					<img src=\"images/delete.png\" onclick=\"remItem(event,'user','".$array['userid']."',1)\" />
				</td>
";
				}
				echo "</tr>";
				if($switch_list){$switch_list = 0;}
				else{$switch_list = 1;}
			}
		}
		echo "
		</table>
		<br>
";
		showPageIndex($_POST['pagename'],$_POST['tabname'],$_POST['page'],$pages,$_POST['action']);
		echo"
		<br>
	</div>
";
		if($_POST['action'] > 0){
			echo "<div align=\"center\"><a href=\"#\" onclick=\"showPrevTab();\">Close</div>";
		}
		$db->close();
	}
?>
