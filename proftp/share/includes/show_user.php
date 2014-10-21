<?php
	session_start();
	require_once("../functions/functions.php");

	if(isset($_SESSION['login']['username'])){
?>
	<div class="tabs">
		<table class="tabs">
			<tr>
				<td class="tablink" onclick="showTab('user','user','1','0');">User</td>
				<td class="tablink" onclick="showTab('user','group','1','0');">Group</td>
				<td class="tablink" onclick="showTab('user','key','1','0');">Key</td>
				<td class="tabexpand"><img src="images/arrow_out.png" onclick="expandResult(0);" /></td>
			</tr>
		</table>
	</div>
	<br>
	<div id="resulttab"></div>
	<script>
		showTab('user','user','1','0');
	</script>
<?php
	}
?>
