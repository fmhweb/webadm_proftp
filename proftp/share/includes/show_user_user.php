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
		<div align=\"center\" id=\"resultremove\"></div>
";
			showPageIndex($_POST['pagename'],$_POST['tabname'],$_POST['page'],$pages,$_POST['action']);
			echo "
		<br>
";
		echo "<table class=\"detailstitle\" align=\"center\">";
		echo "
			<tr>
				<td class=\"detailsval\" colspan=\"6\">
					<a href=\"#\" onclick=\"showDetails('0','user');return false;\">
						<img src=\"images/user.png\" />
						Add user
					</a>
				</td>
			</tr>
";
		if($db->num_rows($result) > 0){
			echo "
			<tr>
				<th class=\"detailstitleleft\">User</th>
				<th class=\"detailstitleleft\">Surname</th>
				<th class=\"detailstitleleft\">Firstname</th>
				<th class=\"detailstitleleft\">Company</th>
				<th></th>
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
				<td>
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
	</div>
";
			if($_POST['action'] == 1){
				echo "<div align=\"center\"><a href=\"#\" onclick=\"showPrevPage();return false;\">Close</div>";
			}
		}
		$db->close();
	}
?>
