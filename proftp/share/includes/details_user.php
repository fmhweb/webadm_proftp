<?php
	session_start();
	if($_SESSION['login']['username']){
		$checked = array(""," checked");
		$selected = array("per_session_false" => " selected","per_session_true" => "","limit_type_soft" => " selected","limit_type_hard" => "");
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		
		$details = array();
		$tables = array("users" => "userid","quotalimits" => "nameid","quotatallies" => "nameid");
		foreach($tables as $table => $id){
			$query = "SELECT * FROM $table WHERE $id = '".$_POST['id']."';";
			$result = $db->query($query);
			if($db->num_rows($result) > 0){
				$array = $db->fetch_array_assoc($result);
				foreach($array as $key => $val){
					$details[$key] = $val;
				}
				if($table == "quotalimits"){
					if($details['per_session'] == "true"){
						$selected['per_session_flase'] = "";
						$selected['per_session_true'] = " selected";
					}
					if($details['limit_type'] == "hard"){
						$selected['limit_type_soft'] = "";
						$selected['limit_type_hard'] = " selected";
					}
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
				if($table == "users"){
					$details['created_by'] = $_SESSION['login']['username'];
					$details['homedir'] = $_SESSION['ftphome']['path'];
				}
				elseif($table == "quotatallies"){
					$details['bytes_in_used'] = "n/a";
					$details['bytes_out_used'] = "n/a";
					$details['bytes_xfer_used'] = "n/a";
					$details['files_in_used'] = "n/a";
					$details['files_out_used'] = "n/a";
					$details['files_xfer_used'] = "n/a";
				}
			}
		}
		$details['files_in_used_percent'] = 0;
		$details['files_out_used_percent'] = 0;
		$details['files_xfer_used_percent'] = 0;
		$details['bytes_in_used_percent'] = 0;
		$details['bytes_out_used_percent'] = 0;
		$details['bytes_xfer_used_percent'] = 0;
		if($details['bytes_in_avail'] && $details['bytes_in_used'] != "n/a"){$details['bytes_in_used_percent'] = round((100 / $details['bytes_in_avail']) * $details['bytes_in_used'],2);}
		if($details['bytes_out_avail'] && $details['bytes_out_used'] != "n/a"){$details['bytes_out_used_percent'] = round((100 / $details['bytes_out_avail']) * $details['bytes_out_used'],2);}
		if($details['bytes_xfer_avail'] && $details['bytes_xfer_used'] != "n/a"){$details['bytes_xfer_used_percent'] = round((100 / $details['bytes_xfer_avail']) * $details['bytes_xfer_used'],2);}
		if($details['files_in_avail'] && $details['files_in_used'] != "n/a"){$details['files_in_used_percent'] = round((100 / $details['files_in_avail']) * $details['files_in_used'],2);}
		if($details['files_out_avail'] && $details['files_out_used'] != "n/a"){$details['files_out_used_percent'] = round((100 / $details['files_out_avail']) * $details['files_out_used'],2);}
		if($details['files_xfer_avail'] && $details['files_xfer_used'] != "n/a"){$details['files_xfer_used_percent'] = round((100 / $details['files_xfer_avail']) * $details['files_xfer_used'],2);}
		echo "
	<div class=\"title\" onclick=\"showElement('detailsuser');\">User</div>
	<br>
	<div class=\"block\" id=\"detailsuser\">
		<div align=\"center\" id=\"resultsaveuser\"></div>
		<form name=\"formuser\">
		<table align=\"center\" class=\"detailstitle\">
			<tr>
				<td class=\"detailstitle\">Username:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"user[userid]\" id=\"useriduser\" value=\"".$details['userid']."\" /></td>
				<td class=\"detailstitle\">Password:<br><span class=\"infosmall\">(".$_SESSION['default']['min_passwd_length']."-".$_SESSION['default']['max_passwd_length']." characters)</span></td><td class=\"detailsval\"><input class=\"details\" type=\"password\" name=\"password[passwd]\" value=\"\" /><br><input class=\"details\" type=\"password\" name=\"password[repasswd]\" value=\"\" /></td>
			</tr>
			<tr>
				<td class=\"detailstitle\">Surname:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"user[surname]\" value=\"".$details['surname']."\" /></td>
				<td class=\"detailstitle\">Firstname:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"user[firstname]\" value=\"".$details['firstname']."\" /></td>
			</tr>
			<tr>
				<td class=\"detailstitle\">Email:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"user[email]\" value=\"".$details['email']."\" /></td>
				<td class=\"detailstitle\">Telephon:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"user[telephon]\" value=\"".$details['telephon']."\" /></td>
			</tr>
			<tr>
				<td class=\"detailstitle\">Company:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"user[company]\" value=\"".$details['company']."\" /></td>
				<td class=\"detailstitle\" colspan=\"2\"></td>
			</tr>
			<tr>
				<td class=\"detailstitle\">Address:</td><td class=\"detailsval\"><textarea class=\"details\" name=\"user[address]\">".$details['address']."</textarea></td>
				<td class=\"detailstitle\">Comment:</td><td class=\"detailsval\"><textarea class=\"details\" name=\"user[comment]\">".$details['comment']."</textarea></td>
			</tr>
			<tr>
				<th class=\"detailstitle\" colspan=\"4\">Settings</th>
			</tr>
			<tr>
				<td class=\"detailstitle\">Expires:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"user[expires]\" id=\"expires\" value=\"".$details['expires']."\" /></td>
				<td class=\"detailstitle\">Disabled:</td><td class=\"detailsval\"><input type=\"checkbox\" name=\"user[disabled]\"".$checked[$details['disabled']]." /></td>
			</tr>
			<tr>
				<td class=\"detailstitle\">Homedir:</td><td class=\"detailsval\"><input onclick=\"showTab('folder','tree','1','1');\" class=\"details\" type=\"text\" name=\"user[homedir]\" id=\"homedir\" value=\"".$details['homedir']."\" dsiabled /></td>
				<td class=\"detailstitle\">Shell:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"user[shell]\" value=\"".$details['shell']."\" /></td>
			</tr>
			<tr>
				<th class=\"detailstitle\" colspan=\"4\">Info</th>
			</tr>
			<tr>
				<td class=\"detailstitle\">Uid:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"info[uid]\" value=\"".$details['uid']."\" disabled /></td>
				<td class=\"detailstitle\">Gid:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"info[gid]\" value=\"".$details['gid']."\" disabled /></td>
			</tr>
			<tr>
				<td class=\"detailstitle\">Created by:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"info[created_by]\" value=\"".$details['created_by']."\" disabled /></td>
				<td class=\"detailstitle\">Created:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"info[created]\" value=\"".$details['created']."\" disabled /></td>
			</tr>
			<tr>
				<td class=\"detailstitle\">Updated by:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"info[created_by]\" value=\"".$details['updated_by']."\" disabled /></td>
				<td class=\"detailstitle\">Updated:</td><td class=\"detailsval\"><input class=\"details\" type=\"text\" name=\"info[created]\" value=\"".$details['updated']."\" disabled /></td>
			</tr>
		</table>
		<br>
		<div align=\"center\">
";
			if(!$details['userid']){
				echo "<img title=\"Save\" src=\"images/disk.png\" onclick=\"document.getElementById('useridquota').value = document.getElementById('useriduser').value;saveItem('user');setTimeout(function(){saveItem('quota')},200);\" />\n";
			}
			else{
				echo "<img title=\"Save\" src=\"images/disk.png\" onclick=\"saveItem('user');\" />\n";
			}
			
			echo "
		</div>
		</form>
		<br>
	</div>
	<br>
	<div class=\"title\" onclick=\"showElement('detailsquota');\">User quota</div>
	<br>
	<div class=\"block\" id=\"detailsquota\">
		<div align=\"center\" id=\"resultsavequota\"></div>
		<form name=\"formquota\">
		<input type=\"hidden\" name=\"user[userid]\" id=\"useridquota\" value=\"".$details['userid']."\" />
		<table align=\"center\" class=\"detailstitle\">
			<tr>
				<th class=\"detailstitle\" colspan=\"4\">Quota settings</th>
			</tr>
			<tr>
				<td class=\"detailstitle\">Quotas per session:</td><td class=\"detailsval\"><select class=\"details\" name=\"quota[per_session]\"><option".$selected['per_session_false'].">False</option><option".$selected['per_session_true'].">True</option></select></td>
			<td class=\"detailstitle\">Limit:</td><td class=\"detailsval\"><select class=\"details\" name=\"quota[limit_type]\"><option".$selected['limit_type_soft'].">Soft</option><option".$selected['limit_type_hard'].">Hard</option></select></td>
			</tr>
		</table>
		<table align=\"center\" class=\"detailstitle\">
			<tr>
				<th class=\"detailstitle\" colspan=\"4\">Quotas bytes</th>
			</tr>
			<tr>
				<td class=\"detailstitletop\">Bytes in available</td>
				<td class=\"detailstitletop\">Bytes out available</td>
				<td class=\"detailstitletop\">Bytes in/out available</td>
			</tr>
			<tr>
				<td class=\"detailsvaltop\"><input class=\"detailsquota\" type=\"text\" name=\"quota[bytes_in_avail]\" value=\"".$details['bytes_in_avail']."\" onkeyup=\"calcBytes(this.value,'resultsavequota');\" /></td>
				<td class=\"detailsvaltop\"><input class=\"detailsquota\" type=\"text\" name=\"quota[bytes_out_avail]\" value=\"".$details['bytes_out_avail']."\" onkeyup=\"calcBytes(this.value,'resultsavequota');\" /></td>
				<td class=\"detailsvaltop\"><input class=\"detailsquota\" type=\"text\" name=\"quota[bytes_xfer_avail]\" value=\"".$details['bytes_xfer_avail']."\" onkeyup=\"calcBytes(this.value,'resultsavequota');\" /></td>
			</tr>
			<tr>
				<td class=\"detailsvaltop\">".$details['bytes_in_used_percent']."%</td>
				<td class=\"detailsvaltop\">".$details['bytes_out_used_percent']."%</td>
				<td class=\"detailsvaltop\">".$details['bytes_xfer_used_percent']."%</td>
			</tr>
			<tr>
				<td class=\"detailsvaltop\"><input class=\"detailsquota\" type=\"text\" name=\"info[bytes_in_used]\" value=\"".$details['bytes_in_used']."\" disabled /></td>
				<td class=\"detailsvaltop\"><input class=\"detailsquota\" type=\"text\" name=\"info[bytes_out_used]\" value=\"".$details['bytes_out_used']."\" disabled /></td>
				<td class=\"detailsvaltop\"><input class=\"detailsquota\" type=\"text\" name=\"info[bytes_xfer_used]\" value=\"".$details['bytes_xfer_used']."\" disabled /></td>
			</tr>
			<tr>
				<td class=\"detailstitletop\">Bytes in used</td>
				<td class=\"detailstitletop\">Bytes out used</td>
				<td class=\"detailstitletop\">Bytes in/out used</td>
			</tr>
			<tr>
				<th class=\"detailstitle\" colspan=\"4\">Quotas files</th>
			</tr>
			<tr>
				<td class=\"detailstitletop\">Files in available</td>
				<td class=\"detailstitletop\">Files out available</td>
				<td class=\"detailstitletop\">Files in/out available</td>
			</tr>
			<tr>
				<td class=\"detailsvaltop\"><input class=\"detailsquota\" type=\"text\" name=\"files_in_avail\" value=\"".$details['files_in_avail']."\" /></td>
				<td class=\"detailsvaltop\"><input class=\"detailsquota\" type=\"text\" name=\"files_out_avail\" value=\"".$details['files_out_avail']."\" /></td>
				<td class=\"detailsvaltop\"><input class=\"detailsquota\" type=\"text\" name=\"files_xfer_avail\" value=\"".$details['files_xfer_avail']."\" /></td>
			</tr>
			<tr>
				<td class=\"detailsvaltop\">".$details['files_in_used_percent']."%</td>
				<td class=\"detailsvaltop\">".$details['files_out_used_percent']."%</td>
				<td class=\"detailsvaltop\">".$details['files_xfer_used_percent']."%</td>
			</tr>
			<tr>
				<td class=\"detailsvaltop\"><input class=\"detailsquota\" type=\"text\" name=\"files_in_used\" value=\"".$details['files_in_used']."\" disabled /></td>
				<td class=\"detailsvaltop\"><input class=\"detailsquota\" type=\"text\" name=\"files_out_used\" value=\"".$details['files_out_used']."\" disabled /></td>
				<td class=\"detailsvaltop\"><input class=\"detailsquota\" type=\"text\" name=\"files_xfer_used\" value=\"".$details['files_xfer_used']."\" disabled /></td>
			</tr>
			<tr>
				<td class=\"detailstitletop\">Files in used</td>
				<td class=\"detailstitletop\">Files out used</td>
				<td class=\"detailstitletop\">Files in/out used</td>
			</tr>
";
		echo "
		</table>
		<br>
		<div align=\"center\">
			<img title=\"Save\" src=\"images/disk.png\" onclick=\"saveItem('quota');\" />
		</div>
		</form>
		<br>
	</div>
";
		$query = "SELECT * FROM acl WHERE userid = '".$details['userid']."' ORDER BY path;";
		$result = $db->query($query);
		if($db->num_rows($result) > 0){
			echo "
	<br>
	<div class=\"title\" onclick=\"showElement('detailsacl');\">User acl</div>
	<br>
	<div class=\"block\" id=\"detailsacl\">
		<div align=\"center\" id=\"resultsaveacl\"></div>
		<table align=\"center\" class=\"detailstitle\">
			<tr>
				<th class=\"detailstitletop\" colspan=\"10\">User ACLs</th>
			</tr>
			<tr>
				<th class=\"detailstitletop\">Path</th>
				<th class=\"detailstitletop\">Read</th>
				<th class=\"detailstitletop\">Write</th>
				<th class=\"detailstitletop\">Delete</th>
				<th class=\"detailstitletop\">Create</th>
				<th class=\"detailstitletop\">Modify</th>
				<th class=\"detailstitletop\">Move</th>
				<th class=\"detailstitletop\">View</th>
				<th class=\"detailstitletop\">Navigate</th>
				<th></th>
			</tr>
";
			$switch_list = 1;
			$checked = array("false" => "","true" => " checked");
			while($array = $db->fetch_array_assoc($result)){
				echo "
			<tr>
				<td class=\"detailsvaltop\">".$array['path']."</td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"read".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$array['path']."');\";".$checked[$array['read_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"write".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$array['path']."');\";".$checked[$array['write_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"delete".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$array['path']."');\";".$checked[$array['delete_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"create".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$array['path']."');\";".$checked[$array['create_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"modify".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$array['path']."');\";".$checked[$array['modify_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"move".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$array['path']."');\";".$checked[$array['move_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"view".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$array['path']."');\";".$checked[$array['view_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"navigate".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$array['path']."');\";".$checked[$array['navigate_acl']]." /></td>
				<td class=\"detailsvaltop\"><img src=\"images/delete.png\" onclick=\"remAcl('".$array['userid']."','".$array['path']."',1);\" /></td>
			</tr>
";
				if($switch_list){$switch_list = 0;}
				else{$switch_list = 1;}
			}
			echo"
		</table>
	</div>
";
		}
		$db->close();
	}
?>
<script>
	$('#expires').datetimepicker({
		lang:'en',
		step:10,
		format:'Y-m-d H:i:00'
	});
</script>
