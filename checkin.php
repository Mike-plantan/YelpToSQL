<?php

## get the file
$json = file_get_contents("yelp_academic_dataset_checkin.json");
$arr = explode("\n", $json);

include("db.php");
$db = new db();



// Function to convert stdClass to array
function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}

function convertDayToString($d){
	if($d == 0)
		return "Sunday";
	elseif($d == 1)
		return "Monday";
	elseif($d == 2)
		return "Tuesday";
	elseif($d == 3)
		return "Wednesday";
	elseif($d == 4)
		return "Thursday";
	elseif($d == 5)
		return "Friday";
	elseif($d == 6)
		return "Satarday";
}

foreach($arr as $checkin){
	$checkin = json_decode($checkin);
	$checkinInfoArray = objectToArray($checkin->checkin_info);
	$business_id =  $checkin->business_id;

	foreach($checkinInfoArray as $key => $value){
		// split the key
		$dayAndStartTime = explode("-", $key);

		// convert the day from number to string
		$dayID = $dayAndStartTime[1];
		$day = convertDayToString($dayID);

		// Get the start hour
		$startHour = $dayAndStartTime[0];
		$start_hour_with_m = $startHour.":00:00";
		$startHour_with_string = strtotime($start_hour_with_m);
		$start_time = date("H:i:s", $startHour_with_string);
		$end_time = date("H:i:s", $startHour_with_string + 3600);
		$db->execute("INSERT INTO checkin VALUES(?,?,?,?,?)",$business_id,$day,$start_time,$end_time, $value);
	}
}


?>