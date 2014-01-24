<?php error_reporting(E_ALL); require_once("setup.inc.php"); ?>

<?php 

	$db = getConnection();

	$FILE = 'data-full.json.gz';
	$LAST_UPDATED = getLastUpdated($FILE);
	$COMPETITION_ID = COMPETITION_ID;
	$json_updated = '0000-00-00 00:00:00';

	$skill_sql = "SELECT ct.id as 'ctid', ct.competition_trade_id FROM competition_trades ct LEFT JOIN internal_trades it ON it.id = ct.internal_trade_id LEFT JOIN competition_trade_sectors cts ON cts.id = ct.host_sector_id 
	WHERE ct.competition_id = {$COMPETITION_ID} ORDER BY ct.competition_trade_id ASC;";


	$cwd = getcwd();
	$cache_path = $cwd . "/cache/";
	$return_json = array();

	$json_files = array('event_json' => 'data-event.json', 'sector_json' => 'data-sectors.json', 'sponsor_json' => 'data-sponsors.json', 'venue_json' => 'data-venue.json', 'local_push_json' => 'data-local-push.json', 'photos_json' => 'data-photos.json', 'event_schedule_json' => 'data-event-schedule.json');			

	foreach($json_files as $type => $json_file){
		$tmp_json = json_decode(file_get_contents($cache_path . $json_file));
		$return_json[$type] = $tmp_json;
		//$json_updated = ($tmp_json->modified > $json_updated) ? $tmp_json->modified : $json_updated;
	}

	$skill_res = $db->run($skill_sql);
	$skill_names = array();	

	foreach($skill_res as $key=>$val){
		$tmp_json = json_decode(file_get_contents($cache_path . "data-skills-".$val['competition_trade_id'].".json"));		
		
		$json_updated = ($json_updated < $tmp_json->modified) ? $tmp_json->modified : $json_updated;

		$return_json['all_skills_json'][$val['competition_trade_id']] = $tmp_json;
	}//skills	

	$vupdate = false;
	if($LAST_UPDATED < $json_updated || isset($_REQUEST['f'])){
		$update_date = $json_updated;
		writeGzip($FILE, $return_json, $update_date);
		$vupdate = true;
	}//update file


?>
	<?php if (isset($_REQUEST['v'])): ?>
	<?php if($vupdate): ?>
	<p>File updated, Gzip created, <a href='aggregator/cache/<?php echo $FILE; ?>'>download</a></p>
	<?php else: ?>
	<p>File NOT updated because of no changes, <a href='aggregator/cache/<?php echo $FILE; ?>'>download gzip</a><br />
		<a href='index.php?force=data-full.php'>Force update</a></p>
	<?php endif; ?>
		
	<h1>Full Data</h1>
	<pre>
		<?php echo json_encode($return_json); ?>
	</pre>
	<?php endif; ?>