<?php
	session_start();
	if($_SESSION['login']['username']){
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		if($_POST['action'] == 0 && $_POST['id']){
			$query = "SELECT command,params,result,status FROM guicmds WHERE id = ".$_POST['id'].";";
			$result = $db->query($query);
			if($db->num_rows($result) > 0){
				$array = $db->fetch_array_assoc($result);
				if($array['status'] == 1){
					echo "<div class=\"error\">Command failed: '".$array['command']."' - '".$array['params']."' - '".$array['result']."'</div>";
				}
				elseif($array['status'] == 2){
					echo "<img src=\"images/accept.png\" /> Command successfull: '".$array['command']."' - '".$array['params'];
				}
			}
			
		}
		if($_POST['action'] == 1 && $_POST['path']){
			$exitstr = 0;
			$query = "INSERT INTO guicmds (command,params,created_by,created) VALUES ('FOLDER ADD','".$_POST['path']."','".$_SESSION['login']['username']."',NOW());";
			$result = $db->query($query);
			if($db->last_id()){
				$exitstr = $db->last_id();
			}
			echo $exitstr;
		}
		if($_POST['action'] == 2 && $_POST['path']){
			$exitstr = 0;
			$query = "INSERT INTO guicmds (command,params,created_by,created) VALUES ('FOLDER REM','".$_POST['path']."','".$_SESSION['login']['username']."',NOW());";
			$result = $db->query($query);
			if($db->last_id()){
				$exitstr = $db->last_id();
			}
			echo $exitstr;
		}
		if($_POST['action'] == 3){
			$exitstr = 0;
			$query = "INSERT INTO guicmds (command,params,created_by,created) VALUES ('FOLDER RESCAN','','".$_SESSION['login']['username']."',NOW());";
			$result = $db->query($query);
			if($db->last_id()){
				$exitstr = $db->last_id();
			}
			echo $exitstr;
		}
		$db->close();
	}
?>
