<?php
	session_start();
	require_once("../functions/functions.php");

	if(isset($_SESSION['login']['username'])){
?>
	<div class="tabs">
		<table>
			<tr>
				<td class="tablink" onclick="showTab('log','xfer','1','0');">Transfer</td>
			</tr>
		</table>
	</div>
	<br>
	<div id="resulttab"></div>
	<script>
		showTab('log','xfer','1','0');
	</script>
<?php
	}
?>
