<?php
	session_start();
	if($_SESSION['login']['username']){
		include("info_ftpwho.php");
		include("info_proftpd.php");
	}
?>
