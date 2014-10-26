<?php
	session_start();
	require_once("../functions/functions.php");

	if(isset($_SESSION['login']['username'])){
?>
	<div class="tabs">
		<table class="tabs">
			<tr>
				<td class="tablink" onclick="showTab('folder','tree','1','0','');">Folder tree</td>
				<td class="tabexpand"><img src="images/arrow_out.png" onclick="expandResult(0);" /></td>
			</tr>
		</table>
	</div>
	<br>
	<div id="resulttab"></div>
	<script>
		showTab('folder','tree','1','0','');
	</script>
<?php
	}
?>
