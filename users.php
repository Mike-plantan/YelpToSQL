<?php

## get the file
$json = file_get_contents("yelp_academic_dataset_user.json");
$arr = explode("\n", $json);

include("db.php");
$db = new db();


foreach($arr as $user){
	$user = json_decode($user);
	$user->yelping_since = $user->yelping_since."-01";
	$USER_ID = $user->user_id;

	
	$db->execute(
		"INSERT INTO user VALUES(?,?,?,?,?,?)",
		$USER_ID, 
		$user->name, 
		$user->yelping_since, 
		$user->review_count, 
		$user->fans, 
		$user->average_stars
	);
	

	foreach($user->friends as $frID){
		$db->execute("INSERT INTO user_friends VALUES(?,?)",$USER_ID,$frID);
	}

	$db->execute("INSERT INTO user_votes VALUES(?,?,?,?)",$USER_ID, $user->votes->funny, $user->votes->useful, $user->votes->cool);

}	




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