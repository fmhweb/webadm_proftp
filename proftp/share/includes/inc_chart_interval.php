<?php
	if($timediff > 31536000 * 5){#5 Year
		$graphinterval = 31536000;
		$graphintervalstr = "Years";
	}
	elseif($timediff > 2678400 * 3){#3 Month
		$graphinterval = 2678400;
		$graphintervalstr = "Months";
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
	else{
		$graphinterval = 300;
		$graphintervalstr = "5 Minutes";
	}
?>
