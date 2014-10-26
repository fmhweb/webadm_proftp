<?php
	session_start();
	if($_SESSION['login']['username']){
		$exitstr = "";
		$errorstr = "";
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		if($_POST['key']['keyid']){
			if($_POST['remove'] == 0){
				$query = "SELECT keyid FROM `keys` WHERE keyid = '".$_POST['key']['keyid']."';";
				$result = $db->query($query);
				if($db->num_rows($result) == 0){
					$query = "INSERT INTO `keys` (keyid,created_by,created) VALUES ('".$_POST['key']['keyid']."','".$_SESSION['login']['username']."',NOW());";
					$result = $db->query($query);
				}
				$query = "UPDATE `keys` SET comment = '".$_POST['key']['comment']."', `key` = '".$_POST['key']['key']."', updated_by = '".$_SESSION['login']['username']."', updated = NOW() WHERE keyid = '".$_POST['key']['keyid']."';";
				$result = $db->query($query);
				$query = "UPDATE userkeys SET `key` = '".$_POST['key']['key']."', updated_by = '".$_SESSION['login']['username']."', updated = NOW() WHERE keyid = '".$_POST['key']['keyid']."';";
				$result = $db->query($query);
				$exitstr .= "<img src=\"images/accept.png\" />Key updated<br>";
				if($exitstr){echo $exitstr;}
				if($errorstr){echo "<div class=\"error\">$errorstr</div>";}
			}
			elseif($_POST['remove'] == 1){
				if($_POST['level'] == 1){
					echo "Delete key: <img src=\"images/accept.png\" onclick=\"remItem(event,'key','".$_POST['key']['keyid']."',2);\" /><img src=\"images/cross.png\" onclick=\"remItem(event,'key','".$_POST['key']['keyid']."',3);\" />";
				}
				elseif($_POST['level'] == 2){
					$now = new DateTime();
					$query = "DELETE FROM `keys` WHERE keyid = '".$_POST['key']['keyid']."';";
					$result = $db->query($query);
					echo "<img src=\"images/accept.png\" />Key deleted ".$_POST['key']['keyid']." - ".$now->format('Y-m-d H:i:s');
				}
				elseif($_POST['level'] == 3){
					echo "<img src=\"images/cross.png\" />Operation cancelled";
				}
			}
		}
		$db->close();
	}
?>
