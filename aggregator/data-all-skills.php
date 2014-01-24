<?php error_reporting(E_ALL); require_once("setup.inc.php"); ?>

<?php 

	$db = getConnection();

	$FILE = 'data-all-skills.json.gz';
	$LAST_UPDATED = getLastUpdated($FILE);
	$COMPETITION_ID = COMPETITION_ID;
	$json_updated = '0000-00-00 00:00:00';

	$skill_sql = "SELECT ct.id as 'ctid', ct.competition_trade_id FROM competition_trades ct LEFT JOIN internal_trades it ON it.id = ct.internal_trade_id LEFT JOIN competition_trade_sectors cts ON cts.id = ct.host_sector_id 
	WHERE ct.competition_id = {$COMPETITION_ID} ORDER BY ct.competition_trade_id ASC;";


	$skill_res = $db->run($skill_sql);
	$skill_names = array();

	$cwd = getcwd();
	$cache_path = $cwd . "/cache/";

	$return_json = array();

	foreach($skill_res as $key=>$val){
		$tmp_json = json_decode(file_get_contents($cache_path . "data-skills-".$val['competition_trade_id'].".json"));		
		
		$json_updated = ($json_updated < $tmp_json->modified) ? $tmp_json->modified : $json_updated;

		$return_json[] = $tmp_json;
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
	<p>File NOT updated because of no changes, <a href='aggregator/cache/<?php echo $FILE; ?>'>download gzip</a><br /><br />
		<a class='btn btn-warning' href='index.php?force=data-all-skills.php'><i class='icon-white icon-refresh'></i>&nbsp;Force update</a></p>
	<?php endif; ?>

<p><a style='float: left;' class='btn btn-info' href='index.php?edit=skill_descriptions'><i class='icon-white icon-edit'></i>&nbsp;Edit Skill Descriptions</a>
	<a style='float: left; margin-left: 6px;' class='btn btn-info' href='index.php?edit=test_projects'><i class='icon-white icon-edit'></i>&nbsp;Edit Test Projects</a>
	<a style='float: left; margin-left: 6px;' class='btn btn-info' href='index.php?edit=skill_sponsors'><i class='icon-white icon-edit'></i>&nbsp;Edit Skill Sponsors</a>
	<a style='float: left; margin-left: 6px;' class='btn btn-info' href='index.php?edit=skill_highlights'><i class='icon-white icon-edit'></i>&nbsp;Edit Highlight Pictures</a></p>	
	<br style='clear: both;' />
		
	<h1>All Skills</h1>
	<pre>
		<?php echo json_encode($return_json); ?>
	</pre>
	<?php endif; ?>