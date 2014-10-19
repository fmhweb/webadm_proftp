<?php
	session_start();
	$width = 280;
	$height = 280;
	if($_SESSION['login']['username']){
?>
	<div class="tabs">
		<table>
			<tr>
				<td class="tablink" onclick="showTab('monitoring','transfer','1','0');">Transfer charts</td>
				<td class="tablink" onclick="showTab('monitoring','quota','1','0');">Quota charts</td>
				<td class="tablink" onclick="showTab('monitoring','user','1','0');">User charts</td>
			</tr>
		</table>
	</div>
	<br>
	<div id="resulttab"></div>
<?php
	}
?>
