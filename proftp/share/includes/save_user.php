<?php
	session_start();
	if($_SESSION['login']['username']){
		$exitstr = "";
		$errorstr = "";
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		if($_POST['user']['userid']){
			if($_POST['remove'] == 0){
				$query = "SELECT userid FROM users WHERE userid = '".$_POST['user']['userid']."';";
				$result = $db->query($query);
				if($db->num_rows($result) == 0){
					$query = "INSERT INTO users (userid,homedir,created_by,created) VALUES ('".$_POST['user']['userid']."','".$_POST['user']['homedir']."','".$_SESSION['login']['username']."',NOW());";
					$result = $db->query($query);
				}
				$query = "";
				foreach($_POST['user'] as $col => $val){
					if($val){
						if($query){$query .= ", ";}
						$query .= "$col = '$val'";
					}
				}
				if($query){
					$query = "UPDATE users SET $query, gid = '".$_SESSION['default']['min_gid']."', updated_by = '".$_SESSION['login']['username']."', updated = NOW() WHERE userid = '".$_POST['user']['userid']."';";
					$result = $db->query($query);
					$exitstr .= "<img src=\"images/accept.png\" />User updated<br>";
				}
				$query = "SELECT nameid FROM quotalimits WHERE nameid = '".$_POST['user']['userid']."';";
				$result = $db->query($query);
				if($db->num_rows($result) == 0){
					$query = "INSERT INTO quotalimits (nameid,quota_type,per_session,limit_type) VALUES ('".$_POST['user']['userid']."','user','false','hard');";
					$result = $db->query($query);
				}
				if($_POST['password']['passwd']){
					if(!$_POST['password']['repasswd']){
						$errorstr .= "Please confirm the password in the second password field<br>";
					}
					elseif(strlen($_POST['password']['passwd']) < $_SESSION['default']['min_passwd_length'] || strlen($_POST['password']['passwd']) > $_SESSION['default']['max_passwd_length']){
						$errorstr .= "The password must be between ".$_SESSION['default']['min_passwd_length']." - ".$_SESSION['default']['max_passwd_length']." characters long<br>";
					}
					elseif($_POST['password']['passwd'] != $_POST['password']['repasswd']){
						$errorstr .= "The password do not match<br>";
					}
					else{
						//$query = "UPDATE users SET passwd = PASSWORD('".$_POST['password']['passwd']."') WHERE userid = '".$_POST['user']['userid']."';";
						$query = "UPDATE users SET passwd = '".$_POST['password']['passwd']."' WHERE userid = '".$_POST['user']['userid']."';";
						$result = $db->query($query);
						$exitstr .= "<img src=\"images/accept.png\" />Pasword updated<br>";
					}
				}
				$query = "SELECT members FROM groups WHERE groupid = 'ftpuser';";
                                $result = $db->query($query);
				if($db->num_rows($result) == 0){
                                        $query = "INSERT INTO groups (groupid,created_by,created) VALUES ('ftpuser','init',NOW());";
                                        $result = $db->query($query);
					$query = "SELECT members FROM groups WHERE groupid = 'ftpuser';";
					$result = $db->query($query);
                                }
				if($db->num_rows($result) > 0){
					$array = $db->fetch_array_assoc($result);
					$members = split(",",$array['members']);
					$member_str = "";
					$member_found = 0;
					foreach($members as $member){
						if($member_str){$member_str .= ",";}
						$member_str .= trim($member);
						if($member == $_POST['user']['userid']){$member_found = 1;}
					}
					if(!$member_found){
						if($member_str){$member_str .= ",";}
						$member_str .= trim($_POST['user']['userid']);
						$query = "UPDATE groups SET members = '$member_str' WHERE groupid = 'ftpuser';";
						$result = $db->query($query);
					}
                                }
				if($exitstr){echo $exitstr;}
				if($errorstr){echo "<div class=\"error\">$errorstr</div>";}
			}
			elseif($_POST['remove'] == 1){
				if($_POST['level'] == 1){
					echo "Delete user: <img src=\"images/accept.png\" onclick=\"remUser('".$_POST['user']['userid']."',2);\" /><img src=\"images/cross.png\" onclick=\"remUser('".$_POST['user']['userid']."',3);\" />";
				}
				elseif($_POST['level'] == 2){
					$now = new DateTime();
					$query = "DELETE FROM users WHERE userid = '".$_POST['user']['userid']."';";
					$result = $db->query($query);
					$query = "SELECT members FROM groups WHERE groupid = 'ftpuser';";
					$result = $db->query($query);
					if($db->num_rows($result) > 0){
						$array = $db->fetch_array_assoc($result);
						$members = split(",",$array['members']);
						$member_str = "";
						foreach($members as $member){
							if($member != $_POST['user']['userid']){
								if($member_str){$member_str .= ",";}
								$member_str .= $member;
							}
						}
						$query = "UPDATE groups SET members = '$member_str' WHERE groupid = 'ftpuser';";
                                                $result = $db->query($query);
					}
					echo "<img src=\"images/accept.png\" />User deleted ".$_POST['user']['userid']." - ".$now->format('Y-m-d H:i:s');
				}
				elseif($_POST['level'] == 3){
					echo "<img src=\"images/cross.png\" />Operation cancelled";
				}
			}
		}
		$db->close();
	}
?>
