<?php 

	require("aggregator/setup.inc.php");

	$db = getConnection();

	$sql = "SELECT ct.competition_trade_id, it.id FROM competition_trades ct LEFT JOIN internal_trades it ON it.id = ct.internal_trade_id WHERE competition_id = 42;";
	$res = $db->run($sql);

	$skills = array();

	foreach($res as $skill){
		$skills[$skill['id']] = $skill['competition_trade_id'];
	}

	$sql = "SELECT * FROM app_skill_definitions;";
	$res = $db->run($sql);

	foreach($res as $def){
		$definition = mysql_escape_string($def['competition_action_en']);

		//get highlight images
		$sql = "SELECT highlight_images FROM internal_trades WHERE id = " . $def['internal_trade_id'] . ";";
		$res2 = $db->run($sql);
		$images = $res2[0]['highlight_images'];
		$images = json_decode($images);
		$images = array_reverse($images);
		$images = json_encode($images);

		$sql = "UPDATE test_projects SET title_de = '', subtitle_de = '', description_de = '', title_en = 'Competition Action', subtitle_en = 'Judging at the Competition', description_en = '{$definition}', images = '{$images}', modified = NOW() WHERE competition_id = 42 AND competition_trade_id = " . $skills[$def['internal_trade_id']] . ";";
		$db->run($sql);
	}
echo "DONE";
?>