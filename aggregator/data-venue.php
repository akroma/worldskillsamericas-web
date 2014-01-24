<?php error_reporting(E_ALL); require_once("setup.inc.php"); ?>

<?php 
	$db = getConnection();

	$FILE = 'data-venue.json.gz';
	$LAST_UPDATED = getLastUpdated($FILE);

	$lastmod = '0000-00-00 00:00:00';

	/*$venue_details = array(
		'Venue Detail 01' => array(
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
		'Venue Detail 02' => array(
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
		'Venue Detail 03' => array(
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

	$sql = "SELECT * FROM app_venues;";
	$res = $db->run($sql);

	$venue_details = array();

	foreach($res as $r){
		$venue_details[] = array(
			'title' => array(
				'localized_content' => array(
					'EN' => $r['title_en'],
					'DE' => $r['title_de']
					)
				),
			'content' => array(
				'localized_content' => array(
					'EN' => $r['content_en'],
					'DE' => $r['content_de']
					)
				),
			'content_image' => $r['content_image'],
			'modified' => $r['modified']
			);

		$lastmod = ($r['modified'] > $lastmod) ? $r['modified'] : $lastmod;
	}
		
		$vupdate = false;
		if($LAST_UPDATED < $lastmod || isset($_REQUEST['f'])){
				$update_date = $lastmod;
				writeGzip($FILE, $venue_details, $update_date);
				$vupdate = true;
		}//update file

?>
	<?php if (isset($_REQUEST['v'])): ?>
	<?php if($vupdate): ?>
	<p>File updated, Gzip created, <a href='aggregator/cache/<?php echo $FILE; ?>'>download</a></p>
	<?php else: ?>
	<p>File NOT updated because of no changes, <a href='aggregator/cache/<?php echo $FILE; ?>'>download gzip</a><br /><br />
		<a class='btn btn-warning' href='index.php?force=data-venue.php'><i class='icon-white icon-refresh'></i>&nbsp;Force update</a></p>
	<?php endif; ?>
		
	<h1>Venue Information</h1>
	<p><a class='btn btn-info' href='index.php?edit=venues'><i class='icon-white icon-edit'></i>&nbsp;Edit venue data</a></p>
	<pre>
		<?php echo json_encode($venue_details); ?>
	</pre>
	<?php endif; ?>