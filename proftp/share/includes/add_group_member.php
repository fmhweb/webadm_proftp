<?php
	session_start();
	if($_SESSION['login']['username']){
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		if($_POST['groupid'] && $_POST['userid']){
			if($_POST['remove'] == 0){
				$query = "SELECT groupid FROM groupmembers WHERE groupid = '".$_POST['groupid']."' AND userid = '".$_POST['userid']."';";
				$result = $db->query($query);
				if($db->num_rows($result) == 0){
					$query = "INSERT INTO groupmembers (groupid,userid) VALUES ('".$_POST['groupid']."','".$_POST['userid']."');";
					$result = $db->query($query);
					$query = "SELECT * FROM groupacl WHERE groupid = '".$_POST['groupid']."';";
					$result = $db->query($query);
					if($db->num_rows($result) > 0){
						while($array = $db->fetch_array_assoc($result)){
							$query = "INSERT INTO useracl (userid,groupid,path,type,read_acl,write_acl,delete_acl,create_acl,modify_acl,move_acl,view_acl,navigate_acl,created_by,created) VALUES ('".$_POST['userid']."','".$_POST['groupid']."','".$array['path']."','group','".$array['read_acl']."','".$array['write_acl']."','".$array['delete_acl']."','".$array['create_acl']."','".$array['modify_acl']."','".$array['move_acl']."','".$array['view_acl']."','".$array['navigate_acl']."','".$_SESSION['login']['username']."',NOW());";
							$result2 = $db->query($query);
						}
					}
					echo "<img src=\"images/accept.png\" />Members updated<br>";
				}
				else{
					echo "<img src=\"images/cross.png\" />User '".$_POST['userid']."' already a member";
				}
			}
			elseif($_POST['remove'] == 1){
				if($_POST['level'] == 1){
					echo "Delete groupmember: <img src=\"images/accept.png\" onclick=\"addGroupMember('".$_POST['groupid']."','".$_POST['userid']."',1,2);\" /><img src=\"images/cross.png\" onclick=\"addGroupMember('".$_POST['groupid']."','".$_POST['userid']."',1,3);\" />";
				}
				elseif($_POST['level'] == 2){
					$now = new DateTime();
					$query = "DELETE FROM groupmembers WHERE groupid = '".$_POST['groupid']."' AND userid = '".$_POST['userid']."';";
					$result = $db->query($query);
					echo "<img src=\"images/accept.png\" />Groupmember deleted ".$_POST['userid']." - ".$now->format('Y-m-d H:i:s');
				}
				elseif($_POST['level'] == 3){
					echo "<img src=\"images/cross.png\" />Operation cancelled";
				}
			}
		}
		$db->close();
	}
?>
