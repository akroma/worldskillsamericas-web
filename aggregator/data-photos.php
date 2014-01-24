<?php error_reporting(E_ALL); require_once("setup.inc.php"); ?>

<?php 
	$db = getConnection();

	$FILE = 'data-photos.json.gz';
	$LAST_UPDATED = getLastUpdated($FILE);

	$sql = "SELECT * FROM app_photo_highlights";
	$res = $db->run($sql);

	$flickr = array();

	/*$flickr = array(
		'WSC2011 Highlights 1' => 'http://www.flickr.com/photos/worldskills/sets/72157627700971983/',
		'WSC2011 Highlights 2' => 'http://www.flickr.com/photos/worldskills/sets/72157627707823509/',
		'WSC2011 Highlights 3' => 'http://www.flickr.com/photos/worldskills/sets/72157627839084082/',
		'WSC2011 Highlights 4' => 'http://www.flickr.com/photos/worldskills/sets/72157627845885710/',
		'WSC2011 Highlights 5' => 'http://www.flickr.com/photos/worldskills/sets/72157627853762014/',
		'Closing Ceremony' => 'http://www.flickr.com/photos/worldskills/sets/72157627721777733/',
		'Medal Winners' => 'http://www.flickr.com/photos/worldskills/sets/72157627846244442/'		
		);*/

	foreach($res as $r){
		$flickr[$r['title']] = $r['flickr_set_url'];
	}

	$vupdate = false;
	if(true){ //always update
		$update_date = date("Y-m-d H:i:s");
		writeGzip($FILE, $flickr, $update_date);
		$vupdate = true;
	}//update file

?>
	<?php if (isset($_REQUEST['v'])): ?>
	<?php if($vupdate): ?>
	<p>File updated, Gzip created, <a href='aggregator/cache/<?php echo $FILE; ?>'>download</a></p>
	<?php else: ?>
	<p>File NOT updated because of no changes, <a href='aggregator/cache/<?php echo $FILE; ?>'>download gzip</a><br /><br />
		<a class='btn btn-warning' href='index.php?force=data-photos.php'><i class='icon-white icon-refresh'></i>&nbsp;Force update</a></p>
	<?php endif; ?>
		
	<h1>Photos</h1>
	<p><a class='btn btn-info' href='index.php?edit=photos'><i class='icon-white icon-edit'></i>&nbsp;Edit Flickr Feeds</a></p>
	<pre>
		<?php echo json_encode($flickr); ?>
	</pre>	
	<?php endif; ?>