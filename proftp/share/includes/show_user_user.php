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
				<input class=\"filter\" style=\"width:50%;\" type=\"text\" name=\"filter\" value=\"".$_POST['filter']."\" onkeyup=\"evalFilter(event,'user','user','1','0');\" placeholder=\"Filter (eg. userid)\" />
				<span class=\"filterbut\" onclick=\"showTab('user','user','1','0');\">GO</span>
			<form>
		</div>
		<br>
		<div align=\"center\" id=\"resultremove\"></div>
";
			showPageIndex($_POST['pagename'],$_POST['tabname'],$_POST['page'],$pages,$_POST['action']);
			echo "
		<br>
";
		echo "
		<div class=\"options\">
			<a href=\"#\" onclick=\"showDetails('0','user');return false;\">
				<img src=\"images/user.png\" />
				Add user
			</a>
		</div>
		<table class=\"detailstitle\" align=\"center\">
";
		if($db->num_rows($result) > 0){
			echo "
			<tr>
				<th class=\"detailstitleleft\">Userid</th>
				<th class=\"detailstitleleft\">Surname</th>
				<th class=\"detailstitleleft\">Firstname</th>
				<th class=\"detailstitleleft\">Company</th>
				<th class=\"detailstitleleft\">Commands</th>
			</tr>
";
			$switch_list = 1;
			while($array = $db->fetch_array_assoc($result)){
				if($_POST['action'] == 1){
					echo "<tr class=\"list$switch_list\" onclick=\"addAcl('".$array['userid']."','1',current_acl_path);\">";
				}
				else{
					echo "<tr class=\"list$switch_list\">";
				}
				echo "
				<td class=\"detailsval\">".$array['userid']."</td>
				<td class=\"detailsval\">".$array['surname']."</td>
				<td class=\"detailsval\">".$array['firstname']."</td>
				<td class=\"detailsval\">".$array['company']."</td>
				<td class=\"detailsval\">
					<img src=\"images/pencil.png\" onclick=\"showDetails('".$array['userid']."','user');\")\" />
					<img src=\"images/delete.png\" onclick=\"remUser('".$array['userid']."',1)\" />
				</td>
			</tr>
";
				if($switch_list){$switch_list = 0;}
				else{$switch_list = 1;}
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
				echo "<div align=\"center\"><a href=\"#\" onclick=\"showPrevPage();return false;\">Close</div>";
			}
		}
		$db->close();
	}
?>
