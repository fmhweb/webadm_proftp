<?php
	session_start();
	if($_SESSION['login']['username']){
		if($_POST['nameid'] && $_POST['path']){
			require('../classes/mysql.php');
			$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
			$now = new DateTime();
			if($_POST['remove'] == 0){
				$query_ext = "";
				if($_POST['type'] == "user"){$query_ext = " AND type = 'user';";}
				$query = "SELECT * FROM ".$_POST['type']."acl WHERE ".$_POST['type']."id = '".$_POST['nameid']."' AND path = '".$_POST['path']."'$query_ext;";
				$result = $db->query($query);
				$update = 1;
				if($db->num_rows($result) == 0){
					if($_POST['type'] == "group"){
						$query = "INSERT INTO groupacl (groupid,path,created_by,created) VALUES ('".$_POST['nameid']."','".$_POST['path']."','".$_SESSION['login']['username']."',NOW());";
					}
					else{
						$query = "DELETE FROM useracl WHERE userid = '".$_POST['nameid']."' AND path = '".$_POST['path']."' AND type = 'group';";
						$result = $db->query($query);
						$query = "INSERT INTO useracl (userid,groupid,path,type,created_by,created) VALUES ('".$_POST['nameid']."','webadm','".$_POST['path']."','user','".$_SESSION['login']['username']."',NOW());";
					}
					$result = $db->query($query);
					echo "<img src=\"images/accept.png\" />".$_POST['nameid']." added to ".$_POST['path']." - ".$now->format('Y-m-d H:i:s')."<br>";
					$update = 0;
				}
				if($_POST['namenew'] != 1){
					if($update && isset($_POST['read']) && isset($_POST['write']) && isset($_POST['delete']) && isset($_POST['create']) && isset($_POST['modify']) && isset($_POST['move']) && isset($_POST['view']) && isset($_POST['navigate'])){
						$query = "UPDATE ".$_POST['type']."acl SET read_acl = '".$_POST['read']."',write_acl = '".$_POST['write']."',delete_acl = '".$_POST['delete']."',create_acl = '".$_POST['create']."',modify_acl = '".$_POST['modify']."',move_acl = '".$_POST['move']."',view_acl = '".$_POST['view']."',navigate_acl = '".$_POST['navigate']."',updated_by = '".$_SESSION['login']['username']."',updated = NOW() WHERE ".$_POST['type']."id = '".$_POST['nameid']."' AND path = '".$_POST['path']."'$query_ext;";
						//echo "$query<br>";
						$result = $db->query($query);
						if($_POST['type'] == "group"){
							$query = "SELECT userid FROM groupmembers WHERE groupid = '".$_POST['nameid']."';";
							$result = $db->query($query);
							if($db->num_rows($result) != 0){
								while($array = $db->fetch_array_assoc($result)){
									$query = "SELECT userid,type FROM useracl WHERE userid = '".$array['userid']."' AND path = '".$_POST['path']."';";
									$result2 = $db->query($query);
									if($db->num_rows($result2) == 0){
										$array2 = $db->fetch_array_assoc($result2);
										if($array2['type'] != 'user'){
											$query = "INSERT INTO useracl (userid,groupid,path,type,created_by,created) VALUES ('".$array['userid']."','".$_POST['nameid']."','".$_POST['path']."','group','".$_SESSION['login']['username']."',NOW());";
											$result2 = $db->query($query);
										}
									}
								}
									$query = "UPDATE useracl SET read_acl = '".$_POST['read']."',write_acl = '".$_POST['write']."',delete_acl = '".$_POST['delete']."',create_acl = '".$_POST['create']."',modify_acl = '".$_POST['modify']."',move_acl = '".$_POST['move']."',view_acl = '".$_POST['view']."',navigate_acl = '".$_POST['navigate']."',updated_by = '".$_SESSION['login']['username']."',updated = NOW() WHERE groupid = '".$_POST['nameid']."' AND path = '".$_POST['path']."' AND type = 'group';";
									$result2 = $db->query($query);
							}
						}
						echo "<img src=\"images/accept.png\" />Acl updated for ".$_POST['type']." ".$_POST['nameid']." - ".$now->format('Y-m-d H:i:s');
					}
				}
			}
			elseif($_POST['remove'] == 1){
				if($_POST['level'] == 1){
					echo "Delete acl: <img src=\"images/accept.png\" onclick=\"remAcl('".$_POST['nameid']."','".$_POST['type']."','".$_POST['path']."',2);\" /><img src=\"images/cross.png\" onclick=\"remAcl('".$_POST['nameid']."','".$_POST['type']."','".$_POST['path']."',3);\" />";
				}
				elseif($_POST['level'] == 2){
					if($_POST['type'] == "group"){
						$query = "DELETE FROM useracl WHERE groupid = '".$_POST['nameid']."' AND path = '".$_POST['path']."' AND type = 'group';";
						$result = $db->query($query);
						$query = "DELETE FROM groupacl WHERE groupid = '".$_POST['nameid']."' AND path = '".$_POST['path']."';";
						$result = $db->query($query);
					}
					else{
						$query = "DELETE FROM useracl WHERE userid = '".$_POST['nameid']."' AND path = '".$_POST['path']."' AND type = 'user';";
						$result = $db->query($query);
						$query = "SELECT * FROM groupacl a LEFT JOIN groupmembers b ON a.groupid = b.groupid WHERE a.path = '".$_POST['path']."' AND b.userid = '".$_POST['nameid']."';";
						$result = $db->query($query);
						if($db->num_rows($result) != 0){
							while($array = $db->fetch_array_assoc($result)){
								$query = "INSERT INTO useracl (userid,groupid,path,type,read_acl,write_acl,delete_acl,create_acl,modify_acl,move_acl,view_acl,navigate_acl,created_by,created) VALUES ('".$_POST['nameid']."','".$array['groupid']."','".$array['path']."','group','".$array['read_acl']."','".$array['write_acl']."','".$array['delete_acl']."','".$array['create_acl']."','".$array['modify_acl']."','".$array['move_acl']."','".$array['view_acl']."','".$array['navigate_acl']."','".$_SESSION['login']['username']."',NOW());";
								$result2 = $db->query($query);
							}
						}
					}
					echo "<img src=\"images/accept.png\" />Acl deleted for ".$_POST['type']." ".$_POST['nameid']." - ".$now->format('Y-m-d H:i:s');
				}
				elseif($_POST['level'] == 3){
					echo "<img src=\"images/cross.png\" />Operation cancelled";
				}
			}
			$db->close();
		}
	}
?>
