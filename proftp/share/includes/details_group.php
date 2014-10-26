<?php
	session_start();
	if($_SESSION['login']['username']){
		$checked = array(""," checked");
		$selected = array("per_session_false" => " selected","per_session_true" => "","limit_type_soft" => " selected","limit_type_hard" => "");
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		
		$details = array();
		$tables = array("groups" => "groupid");
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
				if($table == "groups"){
					$details['created_by'] = $_SESSION['login']['username'];
				}
			}
		}
		echo "
	<div class=\"tabs\">
		<table>
			<tr>
				<td class=\"tablink\" onclick=\"showGroupElement('detailsgroup');\">Group</td>
				<td class=\"tablink\" onclick=\"showGroupElement('detailsmembers');\">Users</td>
				<td class=\"tablink\" onclick=\"showGroupElement('detailsacl');\">Acl</td>
			</tr>
		</table>
	</div>
	<br>
	<div class=\"block\" id=\"detailsgroup\">
		<br>
		<div align=\"center\" id=\"resultsavegroup\"></div>
		<form name=\"formgroup\">
		<table align=\"center\" class=\"detailstitle\">
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Groupname</span>
					<br>
					<input class=\"details\" type=\"text\" name=\"group[groupid]\" id=\"groupidgroup\" value=\"".$details['groupid']."\" />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Comment</span>
					<br>
					<textarea class=\"details\" name=\"group[comment]\">".$details['comment']."</textarea>
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
		echo "<img title=\"Save\" src=\"images/disk.png\" onclick=\"saveItem('group');\" />\n";
			
		echo "
		</div>
		</form>
		<br>
	</div>
	<div class=\"block\" id=\"detailsmembers\">
		<br>
		<div align=\"center\" id=\"resultsavemembers\"></div>
";
		if($details['groupid']){
			$query = "SELECT userid FROM groupmembers WHERE groupid = '".$details['groupid']."';";
			$result = $db->query($query);
			echo "
		<div class=\"options\">
			<a href=\"#\" onclick=\"showTab('user','user','1','2','".$details['groupid']."');\">
				<img src=\"images/user.png\" />
				Add user
			</a>
		</div>
                <table align=\"center\" class=\"list\">
";
                        if($db->num_rows($result) > 0){
				echo "
			<tr>
				<th class=\"list\" >User</th>
				<th></th>
			</tr>
";
				$switch_list = 1;
                                while($array = $db->fetch_array_assoc($result)){
					 echo "
                        <tr class=\"list$switch_list\">
                                <td class=\"list\">".$array['userid']."</td>
				<td class=\"list\" style=\"text-align:right;\">
					<img src=\"images/delete.png\" onclick=\"addGroupMember('".$details['groupid']."','".$array['userid']."',1,1);\" />
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
                $query = "SELECT * FROM groupacl WHERE groupid = '".$details['groupid']."' ORDER BY path;";
                $result = $db->query($query);
		echo "
        <div class=\"block\" id=\"detailsacl\">
                <br>
                <div align=\"center\" id=\"resultsaveacl\"></div>
                <table align=\"center\" class=\"detailstitle\">
                        <tr>
                                <td colspan=\"10\">
                                        <a href=\"#\" onclick=\"showTab('folder','tree','1','2','".$details['groupid']."');\">
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
                                <td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"read$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$array['path']."');\";".$checked[$array['read_acl']]." /></td>
                                <td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"write$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$array['path']."');\";".$checked[$array['write_acl']]." /></td>
                                <td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"delete$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$array['path']."');\";".$checked[$array['delete_acl']]." /></td>
                                <td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"create$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$array['path']."');\";".$checked[$array['create_acl']]." /></td>
                                <td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"modify$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$array['path']."');\";".$checked[$array['modify_acl']]." /></td>
                                <td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"move$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$array['path']."');\";".$checked[$array['move_acl']]." /></td>
                                <td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"view$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$array['path']."');\";".$checked[$array['view_acl']]." /></td>
                                <td class=\"detailsvalacl\"><input type=\"checkbox\" id=\"navigate$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$array['path']."');\";".$checked[$array['navigate_acl']]." /></td>
                                <td class=\"detailsvalacl\"><img src=\"images/delete.png\" onclick=\"remAcl('".$array['groupid']."','group','".$array['path']."',1);\" /></td>
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
";
		$db->close();
?>
<script>
	var tabname = "<?php echo $_POST['tabname']; ?>";
	if(tabname){showGroupElement(tabname);}
	else{showGroupElement('detailsgroup');}

	$('#expires').datetimepicker({
		lang:'en',
		step:10,
		format:'Y-m-d H:i:00'
	});
</script>
<?php
	}
?>
