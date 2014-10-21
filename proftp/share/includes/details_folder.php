<?php
	session_start();
	if($_SESSION['login']['username']){
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		
		$query = "SELECT * FROM folders WHERE id = '".$_POST['id']."';";
		$result = $db->query($query);
		echo "
	<div class=\"title\" onclick=\"showElement('detailsfolder');\">Folder options</div>
	<br>
	<div class=\"block\" id=\"detailsfolder\">
		<div align=\"center\" id=\"resultsavefolder\"></div>
		<table align=\"center\" class=\"detailstitle\">
";
		if($db->num_rows($result) > 0){
			$array = $db->fetch_array_assoc($result);
			echo "
			<tr>
				<th class=\"detailstitle\" colspan=\"6\">Info</th>
			</tr>
			<tr>
				<td class=\"detailstitle\">Name</td><td class=\"detailsval\">".$array['name']."</td>
				<td class=\"detailstitle\">Path</td><td class=\"detailsval\" colspan=\"4\">".$array['path']."</td>
			</tr>
			<tr>
				<td class=\"detailstitle\">Depth</td><td class=\"detailsval\">".$array['depth']."</td>
				<td class=\"detailstitle\">Created by</td><td class=\"detailsval\">".$array['created_by']."</td>
				<td class=\"detailstitle\">Created</td><td class=\"detailsval\">".$array['created']."</td>
			</tr>
";
		}
		echo "
		</table>
		<br>
		<table align=\"center\" class=\"detailstitle\">
			<tr><th class=\"detailstitle\">Add or remove folder</th></tr>
			<tr>
				<td class=\"detailsvaltop\"><input class=\"detailstop\" type=\"text\" id=\"newsubfolder\" placeholder=\"Folder name\" onkeyup=\"addFolder(event,'resultsavefolder','".$array['path']."',this.value)\" /></td>
			</tr>
			<tr>
				<td class=\"detailsvaltop\"><a href=\"#\">Remove</a></td>
			</tr>
		</table>
	</div>
";
		if($array['path']){
			$acl_path = $array['path'];
			$query = "SELECT * FROM acl WHERE path = '".$array['path']."';";
			$result = $db->query($query);
			echo "
	<br>
	<div class=\"title\" onclick=\"showElement('detailsfolderacl');\">Folder ACLs</div>
	<br>
	<div class=\"block\" id=\"detailsfolderacl\">
		<div align=\"center\" id=\"resultsaveacl\"></div>
		<table align=\"center\" class=\"detailstitle\">
			<tr>
	                	<th class=\"detailstitle\" colspan=\"10\">User ACL</th>
		        </tr>
			<tr>
				<td colspan=\"9\">
					<a href=\"#\" onclick=\"current_acl_path = '".$array['path']."';showPage('user','1','','1');return false;\">
						<img src=\"images/user.png\" />
						Add user
					</a>
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
				while($array = $db->fetch_array_assoc($result)){
					echo "
			<tr class=\"list$switch_list\">
				<td class=\"detailsvaltop\">".$array['userid']."</td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"read".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$acl_path."');\";".$checked[$array['read_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"write".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$acl_path."');\";".$checked[$array['write_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"delete".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$acl_path."');\";".$checked[$array['delete_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"create".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$acl_path."');\";".$checked[$array['create_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"modify".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$acl_path."');\";".$checked[$array['modify_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"move".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$acl_path."');\";".$checked[$array['move_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"view".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$acl_path."');\";".$checked[$array['view_acl']]." /></td>
				<td class=\"detailsvaltop\"><input type=\"checkbox\" id=\"navigate".$array['userid']."\" onclick=\"addAcl('".$array['userid']."','0','".$acl_path."');\";".$checked[$array['navigate_acl']]." /></td>
				<td class=\"detailsvaltop\"><img src=\"images/delete.png\" onclick=\"remAcl('".$array['userid']."','".$acl_path."',1);\" /></td>
			</tr>
";
					if($switch_list){$switch_list = 0;}
					else{$switch_list = 1;}
				}
			}
			echo "
		</table>
	</div>
";
		}
		$db->close();
	}
?>
