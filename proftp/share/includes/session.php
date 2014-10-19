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
				$_SESSION['login']['show_logs'] = 0;
				$_SESSION['login']['show_dashboards'] = 0;
				$_SESSION['login']['last_login'] = 0;
				$_SESSION['login']['max_list_items'] = 0;
				$_SESSION['loaded'] = 0;
				if($db->num_rows($result) > 0){
					while($array = $db->fetch_array_assoc($result)){
						$_SESSION['login']['username'] = $array['username'];
						$_SESSION['login']['edit_folder'] = $array['edit_folder'];
						$_SESSION['login']['edit_user'] = $array['edit_user'];
						$_SESSION['login']['edit_settings'] = $array['edit_settings'];
						$_SESSION['login']['show_logs'] = $array['show_logs'];
						$_SESSION['login']['show_dashboards'] = $array['show_dashboards'];
						$_SESSION['login']['last_login'] = $array['last_login'];
						$_SESSION['login']['max_list_items'] = $array['max_list_items'];
						$_SESSION['login']['max_list_log_items'] = $array['max_list_log_items'];
						$_SESSION['loaded'] = 1;
					}
				}
				$db->close();
			}
			else{"<div class=\"error\">Login error</div>\n";}
		}
		else{"<div class=\"error\">Config XML does not exist</div>\n";}
	}
?>
