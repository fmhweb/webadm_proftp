<?php
	if(!$_SESSION['login']['username'] || true){
		if(file_exists("./config/config.xml")){
			if($_SERVER['PHP_AUTH_USER']){
				$xml = simplexml_load_file("./config/config.xml");
				foreach($xml->children() as $key => $val){
					if(trim($val) == ""){
						foreach($xml->$key->children() as $key2 => $val2){
							$_SESSION[$key][$key2] = trim($val2);
						}
					}
					else{
						$_SESSION[$key] = trim($val);
					}
				}
				require('./classes/mysql.php');
				$db = new Database($_SESSION['mysql']['host'],$_SESSION['mysql']['user'],$_SESSION['mysql']['pass'],$_SESSION['mysql']['db']);
				$query = "SELECT * FROM guiusers WHERE username = '".$_SERVER['PHP_AUTH_USER']."';";
				$result = $db->query($query);
				$_SESSION['login']['username'] = 0;
				$_SESSION['login']['edit_folder'] = 0;
				$_SESSION['login']['edit_user'] = 0;
				$_SESSION['login']['edit_settings'] = 0;
				$_SESSION['login']['chart_animate'] = 0;
				$_SESSION['login']['last_login'] = 0;
				$_SESSION['login']['max_list_items'] = 0;
				$_SESSION['login']['max_list_log_items'] = 0;
				$_SESSION['loaded'] = 0;
				if($db->num_rows($result) > 0){
					while($array = $db->fetch_array_assoc($result)){
						foreach($array as $key => $val){
							$_SESSION['login'][$key] = $val;
						}
					}
					$_SESSION['loaded'] = 1;
				}
				$db->close();
			}
			else{"<div class=\"error\">Login error</div>\n";}
		}
		else{"<div class=\"error\">Config XML does not exist</div>\n";}
	}
?>
