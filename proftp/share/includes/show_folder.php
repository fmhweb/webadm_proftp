<?php
	session_start();
	require_once("../functions/functions.php");

	if(isset($_SESSION['login']['username'])){
?>
	<div class="tabs">
		<table>
			<tr>
				<td class="tablink" onclick="showTab('folder','tree','1','0');">Folder tree</td>
			</tr>
		</table>
	</div>
	<br>
	<div id="resulttab"></div>
	<script>
		showTab('folder','tree','1','0');
	</script>
<?php
	}
?>
