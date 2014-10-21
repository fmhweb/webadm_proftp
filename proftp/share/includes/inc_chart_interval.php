<?php
	if($timediff > 29030400){#1 Year
		$graphinterval = 31556926;
		$graphintervalstr = "Years";
	}
	elseif($timediff > 2419200 * 3){#3 Month
		$graphinterval = 2629744;
		$graphintervalstr = "Months";
	}
	elseif($timediff > 604800 * 4){#4 Week
		$graphinterval = 604800;
		$graphintervalstr = "Weeks";
	}
	elseif($timediff > 86400 * 6){#6 Day
		$graphinterval = 86400;
		$graphintervalstr = "Days";
	}
	elseif($timediff > 86400 * 4){#4 Day
		$graphinterval = 14400;
		$graphintervalstr = "4 Hours";
	}
	elseif($timediff > 86400 * 2){#2 Day
		$graphinterval = 7200;
		$graphintervalstr = "2 Hours";
	}
	elseif($timediff > 3600){#1 Hour
		$graphinterval = 3600;
		$graphintervalstr = "Hours";
	}
	elseif($timediff > 60){#1 Minute
		$graphinterval = 60;
		$graphintervalstr = "Minutes";
	}
	else{
		$graphinterval = 1;
		$graphintervalstr = "Seconds";
	}
?>
