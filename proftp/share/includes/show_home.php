<?php
	session_start();
	if($_SESSION['login']['username']){
		include("info_ftpwho.php");
		include("info_proftpd.php");
	}
?>
<ul>
	<li>
		Folder
		<ul>
			<li>Delete folder</li>
			<li>Rename folder</li>
			<li>Expand last folders after add/rename/delete</li>
			<li>Check if group acls exists when deleting a user acl</li>
		</ul>
	</li>
	<li>
		User
		<ul>
			<li>Keys</li>
		</ul>
	</li>
	<li>
		Logs
		<ul>
			<li>GUI history</li>
		</ul>
	</li>
	<li>
		Monitoring
		<ul>
			<li>Files in/out</li>
			<li>Filesystem/CPU/Mem</li>
		</ul>
	</li>
	<li>
		Settings
		<ul>
			<li>User</li>
			<li>Global</li>
		</ul>
	</li>
</ul>
