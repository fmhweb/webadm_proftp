<?php
	session_start();
	$width = 280;
	$height = 280;
	if($_SESSION['login']['username']){
?>
	<table align="center">
		<tr>
			<td class="piechart">
				<div class="title">Bytes in by user</div>
				<br>
				<canvas id="bytesinbyuser" width="<?php echo $width; ?>" height="<?php echo $height; ?>"></canvas>
				<div class="legend" id="bytesinbyuserlegend"></div>
			</td>
			<td class="piechart">
				<div class="title">Bytes out by user</div>
				<br>
				<canvas id="bytesoutbyuser" width="<?php echo $width; ?>" height="<?php echo $height; ?>"></canvas>
				<div class="legend" id="bytesoutbyuserlegend"></div>
			</td>
			<td class="piechart">
				<div class="title">Bytes in/out by user</div>
				<br>
				<canvas id="bytesxferbyuser" width="<?php echo $width; ?>" height="<?php echo $height; ?>"></canvas>
				<div class="legend" id="bytesxferbyuserlegend"></div>
			</td>
		</tr>
		<tr>
			<td class="piechart">
				<div class="title">Files in by user</div>
				<br>
				<canvas id="filesinbyuser" width="<?php echo $width; ?>" height="<?php echo $height; ?>"></canvas>
				<div class="legend" id="filesinbyuserlegend"></div>
			</td>
			<td class="piechart">
				<div class="title">Files out by user</div>
				<br>
				<canvas id="filesoutbyuser" width="<?php echo $width; ?>" height="<?php echo $height; ?>"></canvas>
				<div class="legend" id="filesoutbyuserlegend"></div>
			</td>
			<td class="piechart">
				<div class="title">Files in/out by user</div>
				<br>
				<canvas id="filesxferbyuser" width="<?php echo $width; ?>" height="<?php echo $height; ?>"></canvas>
				<div class="legend" id="filesxferbyuserlegend"></div>
			</td>
		</tr>
	</table>
	<script>
		getPieChart('bytesinbyuser',1,'bytes_in_used');
		setTimeout(function(){getPieChart('bytesoutbyuser',1,'bytes_out_used')},400);
		setTimeout(function(){getPieChart('bytesxferbyuser',1,'bytes_xfer_used')},800);
		setTimeout(function(){getPieChart('filesinbyuser',1,'files_in_used')},1200);
		setTimeout(function(){getPieChart('filesoutbyuser',1,'files_out_used')},1600);
		setTimeout(function(){getPieChart('filesxferbyuser',1,'files_xfer_used')},2000);
	</script>
<?php
	}
?>
