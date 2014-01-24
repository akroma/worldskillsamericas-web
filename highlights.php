<?php require("aggregator/setup.inc.php"); ?>
<?php 
	$skill_sql = "SELECT ct.id as 'ctid', it.id as 'itid', ct.competition_trade_id, it.name_en, it.name_de, it.name_fr, sd.required_skills_en, sd.required_skills_de, sd.industry_action_en, sd.industry_action_de, sd.competition_action_en, sd.competition_action_de, sd.modified as 'sd_modified', cts.sector_name, cts.sector_name_de, cts.sector_name_fr, ct.modified as 'ct_modified', it.modified as 'it_modified' FROM competition_trades ct LEFT JOIN internal_trades it ON it.id = ct.internal_trade_id LEFT JOIN competition_trade_sectors cts ON cts.id = ct.host_sector_id LEFT JOIN app_skill_definitions sd ON sd.internal_trade_id = it.id
	WHERE ct.competition_id = ".COMPETITION_ID." ORDER BY ct.competition_trade_id ASC;";

	$db = getConnection();
	$skills_res = $db->run($skill_sql);
	$skills_array = array();

	foreach($skills_res as $skill){
		$skills_array[$skill['competition_trade_id']] = $skill;
	}

	$files = array();

	$dh = opendir("skill_highlights");	
	while(($file = readdir($dh)) !== false){
		if($file == "." || $file == "..") continue;

		$filedata = substr($file, 0, -4);
		$filedata = explode("_", $filedata);
		$files[$filedata[0]][] = SITE_URL . "skill_highlights/" . $file;
	}
	
	foreach($files as $key=>$val){

		sort($val);

		$sql = "UPDATE internal_trades SET modified = NOW(), highlight_images = '".json_encode($val)."' WHERE id = " . $skills_array[$key]['itid'] . ";";
		$db->run($sql);		
		echo "Updated skill number {$key}<br />";
	}
?>