<?php
	if($_SESSION['login']['username'] || true){
		if(isset($_SESSION['cmd']['proftpd']) && $_SESSION['cmd']['proftpd']){
			echo "<div class=\"title\">Proftpd</div>\n<br>\n";
			echo "<div class=\"block\">\n";
			$result = shell_exec($_SESSION['cmd']['proftpd']." -v");
			$result = preg_split('/\n/',$result);
			foreach($result as $line){
				if($line){
					echo "\t$line\n";
				}
			}
			echo "</div><br>";
		}
		else{
			echo "<div class=\"error\">ProFTP not found</div><br>";
		}
	}
/*
Defaults:apache        !requiretty
apache ALL=(ALL) NOPASSWD: /usr/sbin/proftpd
*/
?>
