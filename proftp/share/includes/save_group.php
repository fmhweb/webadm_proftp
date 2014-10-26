<?php
	session_start();
	if($_SESSION['login']['username']){
		$exitstr = "";
		$errorstr = "";
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		if($_POST['group']['groupid']){
			if($_POST['remove'] == 0){
				$query = "SELECT groupid FROM groups WHERE groupid = '".$_POST['group']['groupid']."';";
				$result = $db->query($query);
				if($db->num_rows($result) == 0){
					$query = "INSERT INTO groups (groupid,type,created_by,created) VALUES ('".$_POST['group']['groupid']."','user','".$_SESSION['login']['username']."',NOW());";
					$result = $db->query($query);
				}
				$query = "UPDATE groups SET comment = '".$_POST['group']['comment']."', updated_by = '".$_SESSION['login']['username']."', updated = NOW() WHERE groupid = '".$_POST['group']['groupid']."';";
				$result = $db->query($query);
				$exitstr .= "<img src=\"images/accept.png\" />Group updated<br>";
				if($exitstr){echo $exitstr;}
				if($errorstr){echo "<div class=\"error\">$errorstr</div>";}
			}
			elseif($_POST['remove'] == 1){
				if($_POST['level'] == 1){
					echo "Delete group: <img src=\"images/accept.png\" onclick=\"remItem(event,'group','".$_POST['group']['groupid']."',2);\" /><img src=\"images/cross.png\" onclick=\"remItem(event,'group','".$_POST['group']['groupid']."',3);\" />";
				}
				elseif($_POST['level'] == 2){
					$now = new DateTime();
					$query = "DELETE FROM groups WHERE groupid = '".$_POST['group']['groupid']."';";
					$result = $db->query($query);
					echo "<img src=\"images/accept.png\" />Group deleted ".$_POST['group']['groupid']." - ".$now->format('Y-m-d H:i:s');
				}
				elseif($_POST['level'] == 3){
					echo "<img src=\"images/cross.png\" />Operation cancelled";
				}
			}
		}
		$db->close();
	}
?>
