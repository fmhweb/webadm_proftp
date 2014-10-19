<?php
	if($_SESSION['login']['username'] || true){
		if(isset($_SESSION['cmd']['ftpwho']) && $_SESSION['cmd']['ftpwho']){
			echo "<div class=\"block\">\n";
			echo "<div class=\"title\">Ftpwho</div>\n";
			$result = shell_exec($_SESSION['cmd']['ftpwho']);
			$result = preg_split('/\n/',$result);
			foreach($result as $line){
				if($line){
					echo "\t$line<br>\n";
				}
			}
			echo "</div>";
		}
	        else{
        	        echo "<div class=\"error\">ftpwho not found</div>";
	        }
        }
?>
