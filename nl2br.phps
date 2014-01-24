<?php require("aggregator/setup.inc.php"); ?>
<?php 
	$skill_sql = "SELECT * FROM app_skill_definitions";

	$db = getConnection();
	$skills_res = $db->run($skill_sql);
	$skills_array = array();

	foreach($skills_res as $skill){
		$req = nl2br($skill['required_skills_en']);
		$ind = nl2br($skill['industry_action_en']);
		$com = nl2br($skill['competition_action_en']);

		$req = mysql_escape_string($req);
		$ind = mysql_escape_string($ind);
		$com = mysql_escape_string($com);

		$sql = "UPDATE app_skill_definitions SET required_skills_en = \"{$req}\", industry_action_en = \"{$ind}\", competition_action_en = \"{$com}\" WHERE id = " . $skill['id'];
		$db->run($sql);
		//echo $skill['id'] . ", ";
	}


	// $skill_sql = "SELECT * FROM app_sponsors";

	// $db = getConnection();
	// $skills_res = $db->run($skill_sql);
	// $skills_array = array();

	// foreach($skills_res as $skill){
	// 	$req = nl2br($skill['profile_description_en']);

	// 	$req = mysql_escape_string($req);

	// 	$sql = "UPDATE app_sponsors SET profile_description_en = \"{$req}\" WHERE id = " . $skill['id'];
	// 	$db->run($sql);
	// 	//echo $skill['id'] . ", ";
	// }


	exit("DONE");
?>