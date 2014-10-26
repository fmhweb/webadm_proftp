<?php
	session_start();
	$width = 280;
	$height = 280;
	if($_SESSION['login']['username']){
?>
	<div class="tabs">
		<table>
			<tr>
				<td class="tablink" onclick="showTab('monitoring','xfer','1','0','');">Transfer charts</td>
				<td class="tablink" onclick="showTab('monitoring','quota','1','0','');">Quota charts</td>
			</tr>
		</table>
	</div>
	<br>
	<div id="resulttab"></div>
	<script>
		showTab('monitoring','xfer','1','0','');
	</script>
<?php
	}
?>
