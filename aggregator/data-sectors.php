<?php error_reporting(E_ALL); require_once("setup.inc.php"); ?>

<?php 

	$db = getConnection();

	$FILE = 'data-sectors.json.gz';
	$LAST_UPDATED = getLastUpdated($FILE);
	$COMPETITION_ID = COMPETITION_ID;
	$mod = '0000-00-00 00:00:00';

	$skill_sql = "SELECT ct.id as 'ctid', it.id as 'itid', ct.competition_trade_id, it.name_en, it.name_de, it.name_fr, it.description_en, it.description_de, it.description_fr, cts.id as 'sector_id', cts.sector_name, cts.sector_name_de, cts.sector_name_fr, ct.modified as 'ct_modified', it.modified as 'it_modified' FROM competition_trades ct LEFT JOIN internal_trades it ON it.id = ct.internal_trade_id LEFT JOIN competition_trade_sectors cts ON cts.id = ct.host_sector_id 
	WHERE ct.competition_id = {$COMPETITION_ID} ORDER BY ct.competition_trade_id ASC;";


	$skill_res = $db->run($skill_sql);
	$skill_names = array();


	foreach($skill_res as $key=>$val){
		//if($val['competition_trade_id'] != $SKILL_NUMBER) continue; //get out of the loop early
		$skill_names[$val['sector_id']][$val['competition_trade_id']]['localized_content'] = array(
				'EN' => $val['name_en'], 
				'DE' => $val['name_de'], 
				'FR' => $val['name_fr']
				);
	}//skills	

	$sql = "SELECT * FROM competition_trade_sectors WHERE sector_name IS NOT NULL ORDER BY sector_order ASC;";
	$res = $db->run($sql);

	foreach($res as $key=>$val){
		$res[$key]['sector_name'] = array('localized_content' => array(
			'EN' => $val['sector_name'],
			'DE' => $val['sector_name_de'],
			'FR' => $val['sector_name_fr'])
		);

		$res[$key]['sector_description'] = array('localized_content' => array(
			'EN' => $val['sector_description'],
			'DE' => $val['sector_description_de'],
			'FR' => $val['sector_description_fr'])
		);

		foreach($skill_names[$val['id']] as $key2=>$val2){
			$res[$key]['sector_skills'][$key2] = $val2;
		}
		
		unset($res[$key]['sector_name_de']);
		unset($res[$key]['sector_name_fr']);
		unset($res[$key]['sector_description_de']);
		unset($res[$key]['sector_description_fr']);

		$mod = ($val['modified'] > $mod) ? $val['modified'] : $mod;
	}

	$vupdate = false;
	if($LAST_UPDATED < $mod || isset($_REQUEST['f'])){
		$update_date = date("Y-m-d H:i:s");
		writeGzip($FILE, $res, $update_date);
		$vupdate = true;
	}//update file


?>
	<?php if (isset($_REQUEST['v'])): ?>
	<?php if($vupdate): ?>
	<p>File updated, Gzip created, <a href='aggregator/cache/<?php echo $FILE; ?>'>download</a></p>
	<?php else: ?>
	<p>File NOT updated because of no changes, <a href='aggregator/cache/<?php echo $FILE; ?>'>download gzip</a><br /><br />
		<a class='btn btn-warning' href='index.php?force=data-sectors.php'><i class='icon-white icon-refresh'></i>&nbsp;Force update</a></p>
	<?php endif; ?>
		
	<h1>Sectors</h1>
	<p><a class='btn btn-info' href='index.php?edit=sectors'><i class='icon-white icon-edit'></i>&nbsp;Edit Sector Information</a></p>
	<pre>
		<?php echo json_encode($res); ?>
	</pre>
	<?php endif; ?>