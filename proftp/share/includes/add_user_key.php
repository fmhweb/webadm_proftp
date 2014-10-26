<?php
	session_start();
	if($_SESSION['login']['username']){
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		if($_POST['keyid'] && $_POST['userid']){
			if($_POST['remove'] == 0){
				$query = "SELECT `key` FROM `keys` WHERE keyid = '".$_POST['keyid']."';";
				echo "$query<br>";
				$result = $db->query($query);
				if($db->num_rows($result) > 0){
					$array = $db->fetch_array_assoc($result);
					$query = "SELECT keyid FROM userkeys WHERE userid = '".$_POST['userid']."' AND keyid = '".$_POST['keyid']."';";
					echo "$query<br>";
					$result = $db->query($query);
					if($db->num_rows($result) == 0){
						$query = "INSERT INTO userkeys (userid,keyid,`key`,created_by,updated_by,created,updated) VALUES ('".$_POST['userid']."','".$_POST['keyid']."','".$array['key']."','".$_SESSION['login']['username']."','".$_SESSION['login']['username']."',NOW(),NOW());";
						echo "$query<br>";
						$result = $db->query($query);
						echo "<img src=\"images/accept.png\" />Members updated<br>";
					}
					else{
						echo "<img src=\"images/cross.png\" />Key '".$_POST['keyid']."' already a member";
					}
				}
			}
			elseif($_POST['remove'] == 1){
				if($_POST['level'] == 1){
					echo "Delete key: <img src=\"images/accept.png\" onclick=\"addKey('".$_POST['keyid']."','".$_POST['userid']."',1,2);\" /><img src=\"images/cross.png\" onclick=\"addKey('".$_POST['keyid']."','".$_POST['userid']."',1,3);\" />";
				}
				elseif($_POST['level'] == 2){
					$now = new DateTime();
					$query = "DELETE FROM userkeys WHERE keyid = '".$_POST['userid']."' AND userid = '".$_POST['keyid']."';";
					echo "$query<br>";
					$result = $db->query($query);
					echo "<img src=\"images/accept.png\" />Key deleted ".$_POST['keyid']." - ".$now->format('Y-m-d H:i:s');
				}
				elseif($_POST['level'] == 3){
					echo "<img src=\"images/cross.png\" />Operation cancelled";
				}
			}
		}
		$db->close();
	}
?>
