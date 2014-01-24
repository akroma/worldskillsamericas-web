<?php error_reporting(E_ALL); require_once("setup.inc.php"); ?>

<?php 
	$db = getConnection();

	$FILE = 'data-uploads.json.gz';
	$LAST_UPDATED = getLastUpdated($FILE);

	$lastmod = '0000-00-00 00:00:00';

	$sql = "SELECT * FROM app_photo_uploads;";
	$res = $db->run($sql);

	$uploads = array();

	foreach($res as $r){		

		$uploads[] = array(
			'id' => $r['id'],
			'caption' => $r['caption'],
			'skill' => $r['skill'],
			'author' => $r['author'],
			'description' => $r['description'],
			'fileurl' => $r['fileurl'],
			'filename' => $r['filename'],
			'modified' => $r['modified']
			);

		$lastmod = ($r['modified'] > $lastmod) ? $r['modified'] : $lastmod;
	}


	$vupdate = false;
	if($LAST_UPDATED < $lastmod || isset($_REQUEST['f'])){
		$update_date = $lastmod;
		writeGzip($FILE, $uploads, $update_date);
		$vupdate = true;
	}//update file

?>
	<?php if (isset($_REQUEST['v'])): ?>
	<?php if($vupdate): ?>
	<p>File updated, Gzip created, <a href='aggregator/cache/<?php echo $FILE; ?>'>download</a></p>
	<?php else: ?>
	<p>File NOT updated because of no changes, <a href='aggregator/cache/<?php echo $FILE; ?>'>download gzip</a><br /><br />
		<a class='btn btn-warning' href='index.php?force=data-uploads.php'><i class='icon-white icon-refresh'></i>&nbsp;Force update</a></p>
	<?php endif; ?>
		
	<h1>Photo Uploads</h1>	
		<h2>Uploads</h2>
		<table class='table table-striped'>
			<thead>
				<tr>
					<th>From</th>
					<th>Email</th>
					<th>Subject</th>
					<th>Message</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($uploads as $up): ?>
				<tr>
					<td><?php echo $up['caption']; ?></td>
					<td><?php echo $up['skill']; ?></td>
					<td><?php echo $up['author']; ?></td>
					<td><?php echo $up['description']; ?></td>
					<td><a href='<?php echo $up['fileurl']; ?>' target='_blank'><img width='90' height='90' src='<?php echo $up['fileurl']; ?>' alt='<?php echo $up['caption']; ?>' /></a></td>
					<td><?php echo $up['filename']; ?></td>
					<td><?php echo $up['modified']; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<h2>JSON Feed</h2>
		<pre>
			<?php echo json_encode($uploads); ?>
		</pre>
	<?php endif; ?>