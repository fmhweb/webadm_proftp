<?php
	session_start();
	#session_destroy();
?>
<!DOCTYPE html>

<html>
 
<head>
	<title>WebADM ProFTP</title>
	<meta charset="UTF-8" />
	<meta name="WebADM ProFTP" content="WebGUI for Proftp" />
	<link href="css/style.css" type="text/css" rel="stylesheet" />
	<link href="css/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
	<script src="js/functions.js"></script>
	<script src="js/functions_folder.js"></script>
	<script src="js/Chart.js"></script>
	<script src="js/monitoring.js"></script>
	<script src="js/jquery.js"></script>
	<script src="js/jquery.datetimepicker.js"></script>
</head>
 
<body>
<?php
	require_once("includes/session.php");
	if(isset($_SESSION['login']['username'])){
?>
	<div class="menu">
		<table>
			<tr>
				<td class="menutitle">WebADM Proftp</td>
				<td class="menulink" id="linkhome" onclick="showPage('home');">Home</td>
				<td class="menulink" id="linkfolder" onclick="showPage('folder');">Folder</td>
				<td class="menulink" id="linkuser" onclick="showPage('user');">User</td>
				<td class="menulink" id="linklog" onclick="showPage('log');">Logs</td>
				<td class="menulink" id="linkmonitoring" onclick="showPage('monitoring');">Monitoring</td>
				<td class="menulink" id="linksettings" onclick="showPage('settings');">Settings</td>
				<td class="menuright"><?php echo "User: ".$_SESSION['login']['username']." - Last login: ".$_SESSION['login']['last_login']; ?></td>
			</tr>
		</table>
	</div>
	<table align="center" class="result">
		<tr>
			<td class="resultleft" id="resultleft"></td>
			<td class="resultright" id="resultright"></td>
		</tr>
	</table>
	<div id="debug">
	</div>
<?php
	}
?>
<script>
	showPage('home');
</script>
</body>
</html>
