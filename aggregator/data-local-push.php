<?php require_once("setup.inc.php"); ?>
<?php error_reporting(E_ALL); ?>
<?php 
	$db = getConnection();

	//SMP LIVE SERVER
	$smp = file_get_contents("http://app:a8d0PD23gxY@smp.worldskills.org/rest/skillItem/public");
	$smp_array = json_decode($smp);

	$smp2 = array();
	foreach($smp_array as $skill => $val){
		$notif = array();
		foreach($val as $key2=>$val2){
			$notif[] = array('timestamp' => $val2->timestamp,
				'event' => (array)$val2->event);
		}
		$smp2[] = array('skill' => $skill, 'notifications' => $notif);
	}
	$smp = json_encode($smp2);


	//SMP TEST SERVER


	$FILE = 'data-local-push.json.gz';
	$LAST_UPDATED = getLastUpdated($FILE);


	$sql = "SELECT * FROM app_local_push_global ORDER BY timestamp ASC;";
	$res = $db->run($sql);

	$local_push = array('global_notifications' => array(), 'skill_specific_notifications' => array());

	if($res !== false && count($res) > 0){
		foreach($res as $r){
			$local_push['global_notifications'][] = array(
				'timestamp' => $r['timestamp'],
				'event' => array(
					'localized_content' => array(
						'EN' => $r['text_en'],
						'DE' => $r['text_de']
						)
					)
				);
		}//foreach
	}

	$local_push['skill_specific_notifications'] = json_decode($smp);

	/*$local_push = array(
		'global_notifications' => array(
			array('timestamp' => '2013-07-26 14:15:00',
				'event' => array(
					'localized_content' => array(
						'EN' => 'Push message contents',
						'DE' => 'Push message contents'
						)
					)
			),
			array('timestamp' => '2013-07-26 14:30:00',
				'event' => array(
					'localized_content' => array(
						'EN' => 'Push message contents',
						'DE' => 'Push message contents'
						)
					)
			),
			array('timestamp' => '2013-07-26 14:45:00',
				'event' => array(
					'localized_content' => array(
						'EN' => 'Push message contents',
						'DE' => 'Push message contents'
						)
					)
			)
		),
		'skill_specific_notifications' => json_decode($smp)
	//	'modified' => date('Y-m-d H:i:s')
		);*/

	$vupdate = false;
	if(true){ //always update
		$update_date = date("Y-m-d H:i:s");
		writeGzip($FILE, $local_push, $update_date);
		$vupdate = true;
	}//update file

?>
	<?php if (isset($_REQUEST['v'])): ?>
	<?php if($vupdate): ?>
	<p>File updated, Gzip created, <a href='aggregator/cache/<?php echo $FILE; ?>'>download</a></p>
	<?php else: ?>
	<p>File NOT updated because of no changes, <a href='aggregator/cache/<?php echo $FILE; ?>'>download gzip</a><br /><br />
		<a class='btn btn-warning' href='index.php?force=data-local-push.php'><i class='icon-white icon-refresh'></i>&nbsp;Force update</a></p>
	<?php endif; ?>
		
	<h1>Local Push Messages</h1>
	<p><a class='btn btn-info' href='index.php?edit=local-push'><i class='icon-white icon-edit'></i>&nbsp;Edit Local Push Notifications</a></p>
	<pre>
		<?php echo json_encode($local_push); ?>
	</pre>
	<?php endif; ?>