<pre><?php

## get the file
$json = file_get_contents("yelp_academic_dataset_business.json");
$arr = explode("\n", $json);

// get the database handler
// create a database handler
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



$query_to_insert_business = "
INSERT INTO  `DM`.`business` 
( `business_id` , `full_address` , `open` , `city` , `review_count` , `name` , `type` , `state` , `stars` , `latitude` , `longitude` )
 VALUES ( ?,  ?,  ?,  ?,  ?,  ?,  ?,  ?,  ?,  ?,  ? );";

$query_to_insert_business_hours = "INSERT INTO business_hours values(?,?,?,?)";
$query_to_insert_business_categories = "INSERT INTO business_categories values(?,?)";
$query_to_insert_business_parking = "INSERT INTO business_parking values(?,?,?,?,?,?)";
$query_to_insert_business_ambience = "INSERT INTO business_ambience values(?,?,?,?,?,?,?,?,?,?)";

// for each line in the file we will insert the data into the database.
foreach ($arr as $j) {

	// Convert the line to JSON
	$array = json_decode($j);

	// INSERT regular values.
	$db->execute(
		$query_to_insert_business,
		$array->business_id,
		$array->full_address,
		$array->open,
		$array->city,
		$array->review_count,
		$array->name,
		$array->type,
		$array->state,
		$array->stars,
		$array->latitude,
		$array->longitude
	);

	// Inserting the hours to the database.
	$hoursArray = objectToArray($array->hours);
	foreach ($hoursArray as $day => $values) {
		$db->execute($query_to_insert_business_hours,$array->business_id,$day,$values['open'],$values['close']);
	}

	// insert cats to database
	foreach($array->categories as $cat)
		$db->execute($query_to_insert_business_categories,$array->business_id,$cat);
	
	// insert the parking
	$parkingArray = objectToArray($array->attributes->Parking);
	$db->execute(
		$query_to_insert_business_parking,
		$array->business_id,
		$parkingArray['garage'],
		$parkingArray['street'],
		$parkingArray['validated'],
		$parkingArray['lot'],
		$parkingArray['valet']);

	// insert the ambience
	$ambienceArray = objectToArray($array->attributes->Ambience);
	$db->execute(
		$query_to_insert_business_ambience,
		$array->business_id,
		$ambienceArray['romantic'],
		$ambienceArray['intimate'],
		$ambienceArray['touristy'],
		$ambienceArray['hipster'],
		$ambienceArray['divey'],
		$ambienceArray['classy'],
		$ambienceArray['trendy'],
		$ambienceArray['upscale'],
		$ambienceArray['casual']
	);



}

?>