<?php
	session_start();
	require_once("../functions/functions.php");

	if($_SESSION['login']['username']){
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		if(isset($_POST['id'])){
			$query = "SELECT * FROM folders WHERE parent_id = '".$_POST['id']."' ORDER BY path;";
	                $result = $db->query($query);
			if($db->num_rows($result) > 0){
				echo "<ul class=\"folder\">\n";
				while($array = $db->fetch_array_assoc($result)){
					echo "
		<li class=\"folder\" title=\"Expand\" id=\"folder".$array['id']."\">
			<img id=\"folderimg".$array['id']."\" src=\"images/plus.gif\" onclick=\"showSubFolder('".$array['id']."','".$array['name']."','".$_POST['action']."');\" />
";
					if($_POST['action'] == 1){
						echo "<img src=\"images/folder.png\" onclick=\"document.getElementById('homedir').value = '".$array['path']."';\" />";
					}
					else{
						echo "<img src=\"images/folder.png\" onclick=\"showDetails('".$array['id']."','folder');\" />";
					}
					echo "
		".$array['name']."
		</li>
";
				}
				echo "</ul>\n";
			}
		}
		else{
			if($_POST['page'] && $_POST['page'] > 1){$mysql_page = $_POST['page'] * $_SESSION['login']['max_list_items'] - $_SESSION['login']['max_list_items'];}
			else{$mysql_page = 0;}
			$pages = 0;
			$query = "SELECT COUNT(*) as count FROM folders WHERE depth = 1;";
	                $result = $db->query($query);
			if($db->num_rows($result) > 0){
				while($array = $db->fetch_array_assoc($result)){
					$pages = $array['count'];
				}
			}
			if($pages > $_SESSION['login']['max_list_items']){$pages = ceil($pages / $_SESSION['login']['max_list_items']);}
			else{$pages = 1;}
			$query = "SELECT * FROM folders WHERE depth = 1 ORDER BY path LIMIT $mysql_page, ".$_SESSION['login']['max_list_items'].";";
	                $result = $db->query($query);
			if($db->num_rows($result) > 0){
				echo "
	<div class=\"block\">
	<table align=\"center\" class=\"page\">
		<tr>
";
			showPageIndex($_POST['pagename'],$_POST['tabname'],$_POST['page'],$pages,$_POST['action']);
				echo "
		</tr>
";
				echo "
	</table>
	<br>
	<ul class=\"folder\">
";
				while($array = $db->fetch_array_assoc($result)){
					echo "
		<li class=\"folder\" title=\"Expand\" id=\"folder".$array['id']."\">
			<img id=\"folderimg".$array['id']."\" src=\"images/plus.gif\" onclick=\"showSubFolder('".$array['id']."','".$array['name']."','".$_POST['action']."');\" />
";
					if($_POST['action'] == 1){
						echo "<img src=\"images/folder.png\" onclick=\"document.getElementById('homedir').value = '".$array['path']."';\" />";
					}
					else{
						echo "<img src=\"images/folder.png\" onclick=\"showDetails('".$array['id']."','folder');\" />";
					}
					echo "
		".$array['name']."
		</li>
		<script>
			showSubFolder('".$array['id']."','".$array['name']."','".$_POST['action']."');
		</script>
";
				}
				echo "
	</ul>
	</div>
";
				if($_POST['action'] == 1){
					echo "<div align=\"center\"><a href=\"#\" onclick=\"showPrevPage();return false;\">Close</div>";
				}
			}
		}
		$db->close();
	}
?>
