<?php
	session_start();
	$width = 280;
	$height = 280;
	if($_SESSION['login']['username']){
?>
	<div class="tabs">
		<table>
			<tr>
				<td class="tablink" onclick="showTab('settings','user','1','0');">Interface user</td>
				<td class="tablink" onclick="showTab('settings','global','1','0');">Global</td>
			</tr>
		</table>
	</div>
	<br>
	<div id="resulttab"></div>
	<script>
		showTab('settings','user','1','0');
	</script>
<?php
	}
?>
