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
			$query_ext .= " WHERE keyid LIKE '%".$_POST['filter']."%'";
		}
		else{
			$_POST['filter'] = "";
		}
		$query = "SELECT COUNT(*) as count FROM `keys`$query_ext;";
		$result = $db->query($query);
		if($db->num_rows($result) > 0){
			$array = $db->fetch_array_assoc($result);
			$pages = $array['count'];
		}
		if($pages > $_SESSION['login']['max_list_items']){$pages = ceil($pages / $_SESSION['login']['max_list_items']);}
		else{$pages = 1;}

		$query = "SELECT * FROM `keys`$query_ext ORDER BY keyid LIMIT $mysql_page, ".$_SESSION['login']['max_list_items'].";";
		$result = $db->query($query);
		echo "
	<div class=\"block\">
		<br>
		<div align=\"center\">
			<form id=\"formfilter\" onsubmit=\"return false;\">
				<input class=\"filter\" style=\"width:50%;\" type=\"text\" name=\"filter\" value=\"".$_POST['filter']."\" onkeyup=\"evalFilter(event,'user','key','1','0','".$_POST['params']."');\" placeholder=\"Filter (eg. keyid)\" />
				<span class=\"filter\" onclick=\"showTab('user','key','1','0','');\">GO</span>
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
			<a href=\"#\" onclick=\"showDetails('0','key','');\">
				<img src=\"images/key.png\" />
				Add key
			</a>
		</div>
";
		}
		echo "
		<table class=\"list\" align=\"center\">
			<tr>
				<th class=\"list\">key</th>
				<th class=\"list\">Comment</th>
";
		if($_POST['action'] == 0){
		echo "
				<th class=\"list\"></th>
";
		}
		echo "</tr>";
		if($db->num_rows($result) > 0){
			$switch_list = 1;
			while($array = $db->fetch_array_assoc($result)){
				if($_POST['action'] == 1){#addKey(keyid,userid,remove,level)
					echo "<tr class=\"list$switch_list\" onclick=\"addKey('".$array['keyid']."','".$_POST['params']."',0,0);\">";
				}
				else{
					echo "<tr class=\"list$switch_list\" onclick=\"showDetails('".$array['keyid']."','key','');\")\">";
				}
				echo "
				<td class=\"list\">".$array['keyid']."</td>
				<td class=\"list\">".$array['comment']."</td>
";
				if($_POST['action'] == 0){
				echo "
				<td class=\"list\" style=\"text-align:right;\">
					<img src=\"images/delete.png\" onclick=\"remItem(event,'key','".$array['keyid']."',1)\" />
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
		if($_POST['action'] == 1){
			echo "<div align=\"center\"><a href=\"#\" onclick=\"reloadTab();\">Close</div>";
		}
		$db->close();
	}
?>
