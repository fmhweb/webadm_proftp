<?php
	session_start();
	if($_SESSION['login']['username']){
		if($_POST['userid'] && $_POST['path']){
			require('../classes/mysql.php');
			$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
			if($_POST['remove'] == 0){
				$query = "SELECT * FROM acl WHERE userid = '".$_POST['userid']."' AND path = '".$_POST['path']."';";
				$result = $db->query($query);
				$update = 1;
				$now = new DateTime();
				if($db->num_rows($result) == 0){
					$query = "INSERT INTO acl (userid,path,created_by,created) VALUES ('".$_POST['userid']."','".$_POST['path']."','".$_SESSION['login']['username']."',NOW());";
					echo "User ".$_POST['userid']." added to ".$_POST['path']." - ".$now->format('Y-m-d H:i:s')."<br>";
					$result = $db->query($query);
					$update = 0;
				}
				if($update && isset($_POST['read']) && isset($_POST['write']) && isset($_POST['delete']) && isset($_POST['create']) && isset($_POST['modify']) && isset($_POST['move']) && isset($_POST['view']) && isset($_POST['navigate'])){
					$query = "UPDATE acl SET read_acl = '".$_POST['read']."',write_acl = '".$_POST['write']."',delete_acl = '".$_POST['delete']."',create_acl = '".$_POST['create']."',modify_acl = '".$_POST['modify']."',move_acl = '".$_POST['move']."',view_acl = '".$_POST['view']."',navigate_acl = '".$_POST['navigate']."',updated_by = '".$_SESSION['login']['username']."',updated = NOW() WHERE userid = '".$_POST['userid']."' AND path = '".$_POST['path']."';";
					$result = $db->query($query);
					echo "<img src=\"images/accept.png\" />Acl updated for user ".$_POST['userid']." - ".$now->format('Y-m-d H:i:s');
				}
			}
			elseif($_POST['remove'] == 1){
				if($_POST['level'] == 1){
					echo "Delete acl: <img src=\"images/accept.png\" onclick=\"remAcl('".$_POST['userid']."','".$_POST['path']."',2);\" /><img src=\"images/cross.png\" onclick=\"remAcl('".$_POST['userid']."','".$_POST['path']."',3);\" />";
				}
				elseif($_POST['level'] == 2){
					$query = "DELETE FROM acl WHERE userid = '".$_POST['userid']."' AND path = '".$_POST['path']."';";
					$result = $db->query($query);
					echo "<img src=\"images/accept.png\" />Acl deleted for user ".$_POST['userid']." - ".$now->format('Y-m-d H:i:s');
				}
				elseif($_POST['level'] == 3){
					echo "<img src=\"images/cross.png\" />Operation cancelled";
				}
			}
			$db->close();
		}
	}
?>
