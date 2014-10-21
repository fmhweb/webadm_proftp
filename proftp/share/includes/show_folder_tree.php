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
			$query = "SELECT * FROM folders WHERE depth = 1 ORDER BY path;";
	                $result = $db->query($query);
			if($db->num_rows($result) > 0){
				echo "
	<div class=\"block\">
	<br>
	<div class=\"options\">
		<a href=\"#\" onclick=\"\">
			<img src=\"images/arrow_refresh.png\" />
			Rescan tree
		</a>
	</div>
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
		<br>
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
