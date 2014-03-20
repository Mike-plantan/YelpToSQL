<pre><?php

## get the file
$json = file_get_contents("yelp_academic_dataset_tip.json");
$arr = explode("\n", $json);

include("db.php");
$db = new db();


foreach($arr as $tip){
	$tip = json_decode($tip);
	//echo $tip->text;

	$db->execute("INSERT INTO tip values(?,?,?,?,?)",$tip->business_id, $tip->user_id, $tip->text, $tip->date, $tip->likes);
}


?>