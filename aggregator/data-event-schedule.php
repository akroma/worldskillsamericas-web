<?php error_reporting(E_ALL); require_once("setup.inc.php"); ?>

<?php 
	$db = getConnection();

	$FILE = 'data-event-schedule.json.gz';
	$LAST_UPDATED = getLastUpdated($FILE);

	$lastmod = '0000-00-00 00:00:00';

	/*$event_details = array(
		array(
			'title' => array(
				'localized_content' => array(
					'EN' => 'Title 01 EN',
					'DE' => 'Title 01 DE')
				),
			'content_image' => 'http://www.worldskills.org/images/stories/header_banners/website-header-joinery.jpg',
			'content' => array(
				'localized_content' => array(
					'EN' => 'Content HTML EN',
					'DE' => 'Content HTML DE')
				),
			'modified' => '0000-00-00 00:00:00'
			),
		array(
			'title' => array(
				'localized_content' => array(
					'EN' => 'Title 02 EN',
					'DE' => 'Title 02 DE')
				),
			'content_image' => 'http://www.worldskills.org/images/stories/header_banners/website-header-joinery.jpg',
			'content' => array(
				'localized_content' => array(
					'EN' => 'Content HTML EN',
					'DE' => 'Content HTML DE')
				),
			'modified' => '0000-00-00 00:00:00'
			),
		array(
			'title' => array(
				'localized_content' => array(
					'EN' => 'Title 03 EN',
					'DE' => 'Title 03 DE')
				),
			'content_image' => 'http://www.worldskills.org/images/stories/header_banners/website-header-joinery.jpg',
			'content' => array(
				'localized_content' => array(
					'EN' => 'Content HTML EN',
					'DE' => 'Content HTML DE')
				),
			'modified' => '0000-00-00 00:00:00'
			)		
		);*/

	$sql = "SELECT * FROM app_event_schedule;";
	$res = $db->run($sql);

	$event_schedule = array();

	foreach($res as $r){
		$event_schedule[] = $r;

		$lastmod = ($r['modified'] > $lastmod) ? $r['modified'] : $lastmod;
	}


	$vupdate = false;
	if($LAST_UPDATED < $lastmod || isset($_REQUEST['f'])){
		$update_date = $lastmod;
		writeGzip($FILE, $event_schedule, $update_date);
		$vupdate = true;
	}//update file

?>
	<?php if (isset($_REQUEST['v'])): ?>
	<?php if($vupdate): ?>
	<p>File updated, Gzip created, <a href='aggregator/cache/<?php echo $FILE; ?>'>download</a></p>
	<?php else: ?>
	<p>File NOT updated because of no changes, <a href='aggregator/cache/<?php echo $FILE; ?>'>download gzip</a><br /><br />
		<a class='btn btn-warning' href='index.php?force=data-event-schedule.php'><i class='icon-white icon-refresh'></i>&nbsp;Force update</a></p>
	<?php endif; ?>
		
	<h1>Event Schedule</h1>
	<p><a class='btn btn-info' href='index.php?edit=event_schedule'><i class='icon-white icon-edit'></i>&nbsp;Edit event schedule</a></p>
	<pre>
		<?php echo json_encode($event_schedule); ?>
	</pre>
	<?php endif; ?>