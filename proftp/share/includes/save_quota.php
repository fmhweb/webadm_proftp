<?php
	session_start();
	if($_SESSION['login']['username']){
		$exitstr = "";
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		if($_POST['user']['userid'] && $_POST['quota']){
			$query = "";
			foreach($_POST['quota'] as $col => $val){
				if($val){
					if($query){$query .= ", ";}
					$query .= "$col = '$val'";
				}
			}
			if($query){
				$query = "UPDATE quotalimits SET $query WHERE nameid = '".$_POST['user']['userid']."';";
				$result = $db->query($query);
				$exitstr = "<img src=\"images/accept.png\" />Quotas updated";
			}
		}
		$db->close();
		echo $exitstr;
	}
?>
