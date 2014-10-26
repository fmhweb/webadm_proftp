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
	<div class=\"tabs\">
		<table>
			<tr>
				<td class=\"tablink\" onclick=\"showUserElement('detailsuser');\">User</td>
";
		if($details['userid']){
			echo "
				<td class=\"tablink\" onclick=\"showUserElement('detailsmembers');\">Groups</td>
				<td class=\"tablink\" onclick=\"showUserElement('detailsquota');\">Quota</td>
				<td class=\"tablink\" onclick=\"showUserElement('detailsacl');\">Acl</td>
				<td class=\"tablink\" onclick=\"showUserElement('detailskey');\">Keys</td>
";
		}
		echo "
			</tr>
		</table>
	</div>
	<br>
	<div class=\"block\" id=\"detailsuser\">
		<div align=\"center\" id=\"resultsaveuser\"></div>
		<form name=\"formuser\">
		<table align=\"center\" class=\"detailstitle\">
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Username</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"user[userid]\" id=\"useriduser\" value=\"".$details['userid']."\" />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Password (".$_SESSION['default']['min_passwd_length']."-".$_SESSION['default']['max_passwd_length']." characters)</span>
					<br>
					<input class=\"details\" type=\"password\" name=\"password[passwd]\" value=\"\" />
					<br>
					<input class=\"details\" type=\"password\" name=\"password[repasswd]\" value=\"\" />
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Surname</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"user[surname]\" value=\"".$details['surname']."\" />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Firstname</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"user[firstname]\" value=\"".$details['firstname']."\" />
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\" colspan=\"2\">
					<span class=\"detailstitle\">Email</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"user[email]\" value=\"".$details['email']."\" />
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Company</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"user[company]\" value=\"".$details['company']."\" />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Telephon</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"user[telephon]\" value=\"".$details['telephon']."\" />
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Address</span>
					<br>
					<textarea class=\"details\" name=\"user[address]\">".$details['address']."</textarea>
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Comment</span>
					<br>
					<textarea class=\"details\" name=\"user[comment]\">".$details['comment']."</textarea>
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Expires</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"user[expires]\" id=\"expires\" value=\"".$details['expires']."\" />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Disabled</span>
					<br>
					<input type=\"checkbox\" name=\"user[disabled]\"".$checked[$details['disabled']]." />
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Homedir</span>
					<br>
					<input onclick=\"showTab('folder','tree','1','1','');\" class=\"details\" type=\"text\" name=\"user[homedir]\" id=\"homedir\" value=\"".$details['homedir']."\" dsiabled />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Shell</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"user[shell]\" value=\"".$details['shell']."\" />
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Uid</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"info[uid]\" value=\"".$details['uid']."\" disabled />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Gid</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"info[gid]\" value=\"".$details['gid']."\" disabled />
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
	<div class=\"block\" id=\"detailsquota\">
		<br>
		<div align=\"center\" id=\"resultsavequota\"></div>
		<form name=\"formquota\">
		<input type=\"hidden\" name=\"user[userid]\" id=\"useridquota\" value=\"".$details['userid']."\" />
		<table align=\"center\" class=\"detailstitle\">
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Quotas per session</span>
					<br>
					<select class=\"details\" name=\"quota[per_session]\">
						<option".$selected['per_session_false'].">False</option>
						<option".$selected['per_session_true'].">True</option>
					</select>
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Limit</span>
					<br>
					<select class=\"details\" name=\"quota[limit_type]\">
						<option".$selected['limit_type_soft'].">Soft</option>
						<option".$selected['limit_type_hard'].">Hard</option>
					</select>
				</td>
			</tr>
		</table>
		<table align=\"center\" class=\"detailstitle\">
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Bytes in available</span>
					<br>
					<input class=\"detailsquota\" type=\"text\" name=\"quota[bytes_in_avail]\" value=\"".$details['bytes_in_avail']."\" onkeyup=\"calcBytes(this.value,'resultsavequota');\" />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Bytes out available</span>
					<br>
					<input class=\"detailsquota\" type=\"text\" name=\"quota[bytes_out_avail]\" value=\"".$details['bytes_out_avail']."\" onkeyup=\"calcBytes(this.value,'resultsavequota');\" />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Bytes in/out available</span>
					<br>
					<input class=\"detailsquota\" type=\"text\" name=\"quota[bytes_xfer_avail]\" value=\"".$details['bytes_xfer_avail']."\" onkeyup=\"calcBytes(this.value,'resultsavequota');\" />
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\">".$details['bytes_in_used_percent']."%</td>
				<td class=\"detailsval\">".$details['bytes_out_used_percent']."%</td>
				<td class=\"detailsval\">".$details['bytes_xfer_used_percent']."%</td>
			</tr>
			<tr>
				<td class=\"detailsval\">
					<input class=\"detailsquota\" type=\"text\" name=\"info[bytes_in_used]\" value=\"".$details['bytes_in_used']."\" disabled />
					<br>
					<span class=\"detailstitle\">Bytes in used</span>
				</td>
				<td class=\"detailsval\">
					<input class=\"detailsquota\" type=\"text\" name=\"info[bytes_out_used]\" value=\"".$details['bytes_out_used']."\" disabled />
					<br>
					<span class=\"detailstitle\">Bytes out used</span>
				</td>
				<td class=\"detailsval\">
					<input class=\"detailsquota\" type=\"text\" name=\"info[bytes_xfer_used]\" value=\"".$details['bytes_xfer_used']."\" disabled />
					<br>
					<span class=\"detailstitle\">Bytes in/out used</span>
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Files in available</span>
					<br>
					<input class=\"detailsquota\" type=\"text\" name=\"quota[files_in_avail]\" value=\"".$details['files_in_avail']."\" />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Files out available</span>
					<br>
					<input class=\"detailsquota\" type=\"text\" name=\"quota[files_out_avail]\" value=\"".$details['files_out_avail']."\" />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Files in/out available</span>
					<br>
					<input class=\"detailsquota\" type=\"text\" name=\"quota[files_xfer_avail]\" value=\"".$details['files_xfer_avail']."\" />
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\">".$details['files_in_used_percent']."%</td>
				<td class=\"detailsval\">".$details['files_out_used_percent']."%</td>
				<td class=\"detailsval\">".$details['files_xfer_used_percent']."%</td>
			</tr>
			<tr>
				<td class=\"detailsval\">
					<input class=\"detailsquota\" type=\"text\" name=\"info[files_in_used]\" value=\"".$details['files_in_used']."\" disabled />
					<br>
					<span class=\"detailstitle\">Files in used</span>
				</td>
				<td class=\"detailsval\">
					<input class=\"detailsquota\" type=\"text\" name=\"info[files_out_used]\" value=\"".$details['files_out_used']."\" disabled />
					<br>
					<span class=\"detailstitle\">Files out used</span>
				</td>
				<td class=\"detailsval\">
					<input class=\"detailsquota\" type=\"text\" name=\"info[files_xfer_used]\" value=\"".$details['files_xfer_used']."\" disabled />
					<br>
					<span class=\"detailstitle\">Files in/out used</span>
				</td>
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
		$query = "SELECT * FROM useracl WHERE userid = '".$details['userid']."' AND type = 'user' ORDER BY path;";
		$result = $db->query($query);
		echo "
	<div class=\"block\" id=\"detailsacl\">
		<br>
		<div align=\"center\" id=\"resultsaveacl\"></div>
		<table align=\"center\" class=\"detailstitle\">
                        <tr>
                                <td colspan=\"10\">
                                        <a href=\"#\" onclick=\"showTab('folder','tree','1','3','".$details['userid']."');\">
                                                <img src=\"images/folder.png\" />
                                                Add folder
                                        </a>
					<br>
					<br>
                                </td>
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
		if($db->num_rows($result) > 0){
			$idcount = 0;
			while($array = $db->fetch_array_assoc($result)){
				echo "
			<tr class=\"list$switch_list\">
				<td class=\"detailsval\">".$array['path']."</td>
				<td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"read$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$array['path']."');\";".$checked[$array['read_acl']]." /></td>
				<td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"write$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$array['path']."');\";".$checked[$array['write_acl']]." /></td>
				<td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"delete$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$array['path']."');\";".$checked[$array['delete_acl']]." /></td>
				<td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"create$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$array['path']."');\";".$checked[$array['create_acl']]." /></td>
				<td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"modify$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$array['path']."');\";".$checked[$array['modify_acl']]." /></td>
				<td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"move$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$array['path']."');\";".$checked[$array['move_acl']]." /></td>
				<td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"view$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$array['path']."');\";".$checked[$array['view_acl']]." /></td>
				<td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"navigate$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$array['path']."');\";".$checked[$array['navigate_acl']]." /></td>
				<td class=\"detailsvalacl\"><img src=\"images/delete.png\" onclick=\"remAcl('".$array['userid']."','user','".$array['path']."',1);\" /></td>
			</tr>
";
				if($switch_list){$switch_list = 0;}
				else{$switch_list = 1;}
				$idcount++;
			}
		}
		echo"
		</table>
		<br>
	</div>
	<div class=\"block\" id=\"detailsmembers\">
                <br>
                <div align=\"center\" id=\"resultsavemembers\"></div>
";
                if($details['userid']){
                        $query = "SELECT groupid FROM groupmembers WHERE userid = '".$details['userid']."';";
                        $result = $db->query($query);
                        echo "
                <div class=\"options\">
                        <a href=\"#\" onclick=\"showTab('user','group','1','2','".$details['userid']."');\">
                                <img src=\"images/group.png\" />
                                Add group
                        </a>
                </div>
                <table align=\"center\" class=\"list\">
";
                        if($db->num_rows($result) > 0){
                                echo "
                        <tr>
                                <th class=\"list\">User</th>
				<th></th>
                        </tr>
";
                                $switch_list = 1;
                                while($array = $db->fetch_array_assoc($result)){
                                         echo "
                        <tr class=\"list$switch_list\">
                                <td class=\"list\">".$array['groupid']."</td>
				<td class=\"list\" style=\"text-align:right;\">
					<img src=\"images/delete.png\" onclick=\"addGroupMember('".$array['groupid']."','".$details['userid']."',1,1);\" />
				</td>
                        </tr>
";
                                        if($switch_list){$switch_list = 0;}
                                        else{$switch_list = 1;}
                                }
                        }
                        echo "
                </table>
                <br>
        </div>
";
		}
		echo "
	<div class=\"block\" id=\"detailskey\">
                <br>
                <div align=\"center\" id=\"resultsavekey\"></div>
";
                if($details['userid']){
                        $query = "SELECT * FROM userkeys WHERE userid = '".$details['userid']."';";
                        $result = $db->query($query);
                        echo "
                <div class=\"options\">
                        <a href=\"#\" onclick=\"showTab('user','key','1','1','".$details['userid']."');\">
                                <img src=\"images/key.png\" />
                                Add key
                        </a>
                </div>
                <table align=\"center\" class=\"list\">
";
                        if($db->num_rows($result) > 0){
                                echo "
                        <tr>
                                <th class=\"list\">Key</th>
                                <th></th>
                        </tr>
";
                                $switch_list = 1;
                                while($array = $db->fetch_array_assoc($result)){
                                         echo "
                        <tr class=\"list$switch_list\">
                                <td class=\"list\">".$array['keyid']."</td>
                                <td class=\"list\" style=\"text-align:right;\">
                                        <img src=\"images/delete.png\" onclick=\"addKey('".$details['userid']."','".$array['keyid']."',1,1);\" />
                                </td>
                        </tr>
";
                                        if($switch_list){$switch_list = 0;}
                                        else{$switch_list = 1;}
                                }
                        }
                        echo "
                </table>
                <br>
        </div>
";
		}
		$db->close();
?>
<script>
	var tabname = "<?php echo $_POST['tabname']; ?>";
	if(tabname){showUserElement(tabname);}
	else{showUserElement('detailsuser');}

	$('#expires').datetimepicker({
		lang:'en',
		step:10,
		format:'Y-m-d H:i:00'
	});
</script>
<?php
	}
?>
