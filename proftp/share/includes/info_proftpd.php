<?php
	if($_SESSION['login']['username'] || true){
		if(isset($_SESSION['cmd']['proftpd']) && $_SESSION['cmd']['proftpd']){
			echo "<div class=\"block\">\n";
			echo "<div class=\"title\">Proftpd</div>";
			$result = shell_exec($_SESSION['cmd']['proftpd']." -v");
			$result = preg_split('/\n/',$result);
			foreach($result as $line){
				if($line){
					echo "\t$line\n";
				}
			}
			echo "</div>";
		}
		else{
			echo "<div class=\"error\">ProFTP not found</div>";
		}
	}
/*
Defaults:apache        !requiretty
apache ALL=(ALL) NOPASSWD: /usr/sbin/proftpd
*/
?>
