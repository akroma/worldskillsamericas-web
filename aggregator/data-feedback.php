<?php error_reporting(E_ALL); require_once("setup.inc.php"); ?>

<?php 
	$db = getConnection();

	$FILE = 'data-feedback.json.gz';
	$LAST_UPDATED = getLastUpdated($FILE);

	$lastmod = '0000-00-00 00:00:00';

	$sql = "SELECT * FROM app_feedback;";
	$res = $db->run($sql);

	$feedback = array();

	foreach($res as $r){		

		$feedback[] = array(
			'id' => $r['id'],
			'from' => $r['from'],
			'from_email' => $r['from_email'],
			'subject' => $r['subject'],
			'message' => $r['message'],
			'created' => $r['created'],
			'modified' => $r['modified']
			);

		$lastmod = ($r['modified'] > $lastmod) ? $r['modified'] : $lastmod;
	}


	$vupdate = false;
	if($LAST_UPDATED < $lastmod || isset($_REQUEST['f'])){
		$update_date = $lastmod;
		writeGzip($FILE, $feedback, $update_date);
		$vupdate = true;
	}//update file

?>
	<?php if (isset($_REQUEST['v'])): ?>
	<?php if($vupdate): ?>
	<p>File updated, Gzip created, <a href='aggregator/cache/<?php echo $FILE; ?>'>download</a></p>
	<?php else: ?>
	<p>File NOT updated because of no changes, <a href='aggregator/cache/<?php echo $FILE; ?>'>download gzip</a><br /><br />
		<a class='btn btn-warning' href='index.php?force=data-feedback.php'><i class='icon-white icon-refresh'></i>&nbsp;Force update</a></p>
	<?php endif; ?>
		
	<h1>Feedback</h1>	
		<h2>Feedback messages</h2>
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
				<?php foreach($feedback as $feed): ?>
				<tr>
					<td><?php echo $feed['from']; ?></td>
					<td><?php echo $feed['from_email']; ?></td>
					<td><?php echo $feed['subject']; ?></td>
					<td><?php echo $feed['message']; ?></td>
					<td><?php echo $feed['modified']; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<h2>JSON Feed</h2>
		<pre>
			<?php echo json_encode($feedback); ?>
		</pre>
	<?php endif; ?>