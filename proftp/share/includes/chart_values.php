<?php
	session_start();

	function get_color(&$r,&$g,&$b){
		if($b == 15){
			$g++;
			$b = 0;
		}
		if($g == 15){
			$r++;
			$g = 0;
		}
		if($r == 15){
			$r = 0;
		}
		$background = dechex($r).dechex($r).dechex($g).dechex($g).dechex($b).dechex($b);
		$b++;
		return "#".$background;
	}

	if($_SESSION['login']['username']){
		require('../classes/mysql.php');
		$db = new Database($_SESSION['mysql']['host'], $_SESSION['mysql']['user'], $_SESSION['mysql']['pass'], $_SESSION['mysql']['db']);
		$r = 2;
		$g = 4;
		$b = 6;
		$r2 = 2;
		$g2 = 5;
		$b2 = 6;
		if($_POST['action'] == 1 && $_POST['value']){
			$query = "SELECT nameid,".$_POST['value']." FROM quotatallies;";
			$result = $db->query($query);
			$values = array();
			if($db->num_rows($result) > 0){
				$i = 0;
				$smallest_value = 0;
				while($array = $db->fetch_array_assoc($result)){
					if($smallest_value > $array[$_POST['value']] || !$smallest_value){$smallest_value = $array[$_POST['value']];}
					$values[$i]['value'] = $array[$_POST['value']];
					$values[$i]['color'] = get_color($r,$g,$b);
					$values[$i]['highlight'] = get_color($r2,$g2,$b2);
					$values[$i]['label'] = $array['nameid'];
					$i++;
				}
				$count = 0;
				while($smallest_value > 100){
					$smallest_value /= 10;
					$count++;
				}
				if($count){
					$count = pow(10,$count);
					foreach($values as $key => $value){
						$values[$key]['value'] /= $count;
					}
				}
			}
			echo json_encode($values);
		}
		$db->close();
	}
?>
