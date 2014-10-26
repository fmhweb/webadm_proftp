<?php
	session_start();
	if($_SESSION['login']['username']){
		$checked = array(""," checked");
		$selected = array("per_session_false" => " selected","per_session_true" => "","limit_type_soft" => " selected","limit_type_hard" => "");
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		
		$details = array();
		$tables = array("`keys`" => "keyid");
		foreach($tables as $table => $id){
			$query = "SELECT * FROM $table WHERE $id = '".$_POST['id']."';";
			$result = $db->query($query);
			if($db->num_rows($result) > 0){
				$array = $db->fetch_array_assoc($result);
				foreach($array as $key => $val){
					$details[$key] = $val;
				}
			}
			else{
				$query = "SHOW COLUMNS FROM $table;";
				$result = $db->query($query);
				if($db->num_rows($result) > 0){
					while($array = $db->fetch_array_assoc($result)){
						$details[$array['Field']] = $array['Default'];
					}
				}
				$details['created_by'] = $_SESSION['login']['username'];
			}
		}
		echo "
	<div class=\"tabs\">
		<table>
			<tr>
				<td class=\"tablink\" onclick=\"showKeyElement('detailskey');\">Key</td>
				<td class=\"tablink\" onclick=\"showKeyElement('detailsuser');\">Users</td>
			</tr>
		</table>
	</div>
	<br>
	<div class=\"block\" id=\"detailskey\">
		<br>
		<div align=\"center\" id=\"resultsavekey\"></div>
		<form name=\"formkey\">
		<table align=\"center\" class=\"detailstitle\">
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Keyname</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"key[keyid]\" id=\"keyidkey\" value=\"".$details['keyid']."\" />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Comment</span>
					<br>
					<textarea class=\"details\" name=\"key[comment]\">".$details['comment']."</textarea>
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\" colspan=\"2\">
					<span class=\"detailstitle\">Key</span>
                                        <br>
                                        <textarea class=\"details\" name=\"key[key]\">".$details['key']."</textarea>
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Created by</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"info[created_by]\" value=\"".$details['created_by']."\" disabled />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Created</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"info[created]\" value=\"".$details['created']."\" disabled />
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Updated by</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"info[created_by]\" value=\"".$details['updated_by']."\" disabled />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Updated</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"info[created]\" value=\"".$details['updated']."\" disabled />
				</td>
			</tr>
		</table>
		<br>
		<div align=\"center\">
";
		echo "<img title=\"Save\" src=\"images/disk.png\" onclick=\"saveItem('key');\" />\n";
			
		echo "
		</div>
		</form>
		<br>
	</div>
";
		$db->close();
?>
<script>
	var tabname = "<?php echo $_POST['tabname']; ?>";
	if(tabname){showKeyElement(tabname);}
	else{showKeyElement('detailskey');}

	$('#expires').datetimepicker({
		lang:'en',
		step:10,
		format:'Y-m-d H:i:00'
	});
</script>
<?php
	}
?>
