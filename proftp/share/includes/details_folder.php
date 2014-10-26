<?php
	session_start();
	if($_SESSION['login']['username']){
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		
		$query = "SELECT * FROM folders WHERE id = '".$_POST['id']."';";
		$result = $db->query($query);
		echo "
	<div class=\"tabs\">
		<table>
			<tr>
				<td class=\"tablink\" onclick=\"showFolderElement('detailsfolder');\">Options</td>
				<td class=\"tablink\" onclick=\"showFolderElement('detailsacl');\">Acl</td>
			</tr>
		</table>
	</div>
	<br>
	<div class=\"block\" id=\"detailsfolder\">
		<br>
		<div align=\"center\" id=\"resultsavefolder\"></div>
		<table align=\"center\" class=\"detailstitle\">
";
		if($db->num_rows($result) > 0){
			$array = $db->fetch_array_assoc($result);
			echo "
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Name</span>
					<br>
					<input type=\"text\" class=\"details\" value=\"".$array['name']."\" disabled />
				</td>
				<td class=\"detailsval\" colspan=\"2\">
					<span class=\"detailstitle\">Path</span>
					<br>
					<input type=\"text\" class=\"details\" value=\"".$array['path']."\" disabled />
				</td>
			</tr>
			<tr>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Depth</span>
					<br>
					<input type=\"text\" class=\"details\" value=\"".$array['depth']."\" disabled />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Created by</span>
					<br>
					<input type=\"text\" class=\"details\" value=\"".$array['created_by']."\" disabled />
				</td>
				<td class=\"detailsval\">
					<span class=\"detailstitle\">Created</span>
					<br>
					<input type=\"text\" class=\"details\" value=\"".$array['created']."\" disabled />
				</td>
			</tr>
";
		}
		echo "
		</table>
		<br>
		<table align=\"center\">
			<tr><th class=\"list\">Operations</th></tr>
			<tr>
				<td class=\"detailsvaltop\"><input class=\"detailstop\" type=\"text\" id=\"renamefolder\" placeholder=\"Rename folder\" onkeyup=\"renameFolder(event,'resultsavefolder','".$array['path']."',this.value)\" /></td>
			</tr>
			<tr>
				<td class=\"detailsvaltop\"><input class=\"detailstop\" type=\"text\" id=\"newsubfolder\" placeholder=\"Add subfolder\" onkeyup=\"addFolder(event,'resultsavefolder','".$array['path']."',this.value)\" /></td>
			</tr>
			<tr>
				<td class=\"detailsvaltop\"><img src=\"images/delete.png\" onclick=\"remFolder('resultsavefolder','".$array['path']."',1);\" /></td>
			</tr>
		</table>
		<br>
	</div>
";
		if($array['path']){
			$acl_path = $array['path'];
			$query = "SELECT * FROM useracl WHERE path = '$acl_path' AND type = 'user';";
			$result = $db->query($query);
			echo "
	<div class=\"block\" id=\"detailsacl\">
		<br>
		<div align=\"center\" id=\"resultsaveacl\"></div>
		<table align=\"center\" class=\"detailstitle\">
			<tr>
				<th class=\"detailstitle\" colspan=\"10\">Users</th>
			</tr>
			<tr>
				<td colspan=\"10\">
					<a href=\"#\" onclick=\"showTab('user','user','1','1','".$acl_path."');\">
						<img src=\"images/user.png\" />
						Add user
					</a>
					<br>
					<br>
				</td>
			</tr>
";
			if($db->num_rows($result) > 0){
				echo "
			<tr>
				<th class=\"detailstitletop\">User</th>
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
				$idcount = 0;
				while($array = $db->fetch_array_assoc($result)){
					echo "
			<tr class=\"list$switch_list\">
				<td class=\"detailsvaltop\">".$array['userid']."</td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"read$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$acl_path."');\";".$checked[$array['read_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"write$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$acl_path."');\";".$checked[$array['write_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"delete$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$acl_path."');\";".$checked[$array['delete_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"create$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$acl_path."');\";".$checked[$array['create_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"modify$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$acl_path."');\";".$checked[$array['modify_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"move$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$acl_path."');\";".$checked[$array['move_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"view$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$acl_path."');\";".$checked[$array['view_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"navigate$idcount\" onclick=\"addAcl($idcount,'".$array['userid']."','0','user','".$acl_path."');\";".$checked[$array['navigate_acl']]." /></td>
				<td class=\"detailsvaltop\"><img src=\"images/delete.png\" onclick=\"remAcl('".$array['userid']."','user','".$acl_path."',1);\" /></td>
			</tr>
";
					if($switch_list){$switch_list = 0;}
					else{$switch_list = 1;}
					$idcount++;
				}
			}
			$query = "SELECT * FROM groupacl WHERE path = '$acl_path';";
			$result = $db->query($query);
			echo "
		</table>
		<br>
		<table align=\"center\" class=\"detailstitle\">
                        <tr>
                                <th class=\"detailstitle\" colspan=\"10\">Groups</th>
                        </tr>
                        <tr>
                                <td colspan=\"10\">
                                        <a href=\"#\" onclick=\"showTab('user','group','1','1','".$acl_path."');\">
                                                <img src=\"images/group.png\" />
                                                Add group
                                        </a>
					<br>
					<br>
                                </td>
                        </tr>
";
                        if($db->num_rows($result) > 0){
                                echo "
                        <tr>
                                <th class=\"detailstitletop\">Group</th>
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
				$idcount = 0;
                                while($array = $db->fetch_array_assoc($result)){
                                        echo "
                        <tr class=\"list$switch_list\">
                                <td class=\"detailsvaltop\">".$array['groupid']."</td>
                                <td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"read$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$acl_path."');\";".$checked[$array['read_acl']]." /></td>
                                <td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"write$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$acl_path."');\";".$checked[$array['write_acl']]." /></td>
                                <td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"delete$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$acl_path."');\";".$checked[$array['delete_acl']]." /></td>
                                <td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"create$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$acl_path."');\";".$checked[$array['create_acl']]." /></td>
                                <td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"modify$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$acl_path."');\";".$checked[$array['modify_acl']]." /></td>
                                <td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"move$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$acl_path."');\";".$checked[$array['move_acl']]." /></td>
                                <td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"view$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$acl_path."');\";".$checked[$array['view_acl']]." /></td>
                                <td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"navigate$idcount\" onclick=\"addAcl($idcount,'".$array['groupid']."','0','group','".$acl_path."');\";".$checked[$array['navigate_acl']]." /></td>
                                <td class=\"detailsvaltop\"><img src=\"images/delete.png\" onclick=\"remAcl('".$array['groupid']."','group','".$acl_path."',1);\" /></td>
                        </tr>
";
                                        if($switch_list){$switch_list = 0;}
                                        else{$switch_list = 1;}
					$idcount++;
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
	if(tabname){showFolderElement(tabname);}
	else{showFolderElement('detailsfolder');}
</script>
<?php
	}
?>
