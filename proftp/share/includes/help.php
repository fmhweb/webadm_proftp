<?php
	session_start();
	if($_SESSION['login']['username'] || true){
		echo "<div class=\"title\">Help</div>";
		if($_POST['pagename'] == "home"){
?>
Displays information about:
<ul>
	<li>Status proftd</li>
	<li>Status ftpwho - Active connections</li>
</ul>
<?php
		}
	}
?>
