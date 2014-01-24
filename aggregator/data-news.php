<?php error_reporting(E_ALL); require_once("setup.inc.php"); ?>

<?php 
	$db = getConnection();
	$dbj = getJoomlaConnection();

	$FILE = 'data-news.json.gz';
	$LAST_UPDATED = getLastUpdated($FILE);
	$NEWS_UPDATED = '0000-00-00 00:00:00';

	$data = array();

	//get news
	$sql = "SELECT * FROM jos_content WHERE sectionid = 1 AND catid = 114 AND state = 1 AND metakey LIKE '%WSC2013%' ORDER BY modified DESC;";

	$news = $dbj->run($sql);
	

	foreach($news as $key=>$article){

		if($key == 0) $NEWS_UPDATED = $article['created'];

		//WSC2013 FIX
		if(strlen($article['fulltext']) < 10) $article['fulltext'] = $article['introtext'];

		$data[$key] = array(
			'title' => $article['title'],
			'text_short' => strip_tags($article['introtext'], '<b><i><br /><br><br/><p><div><span><ol><ul><li>'),
			'text_full' => strip_tags($article['fulltext'], '<b><i><br /><br><br/><p><div><span><ol><ul><li>'),
			'created' => $article['created'],
			'modified' => $article['modified']
			);

		$delete_a_pattern = "/<a[^>]*><\/a>/";
		//$data[$key]['text_full'] 	= 	preg_replace($delete_a_pattern, "", $data[$key]['text_full']);
		//$data[$key]['text_short'] 	= 	preg_replace($delete_a_pattern, "", $data[$key]['text_short']);
		$data[$key]['text_short'] 	=	preg_replace($delete_a_pattern, "", $data[$key]['text_short']);
		$data[$key]['text_full'] 	=	preg_replace($delete_a_pattern, "", $data[$key]['text_full']);

		//$doc = new DOMDocument();
		//libxml_use_internal_errors(true);
		
		//$doc->loadHTML($article['introtext']); // loads your html
		//$xpath = new DOMXPath($doc);
		//$nodelist = $xpath->query("//img"); // find your image
		//$node = $nodelist->item(0); // gets the 1st image
		//$value = $node->attributes->getNamedItem('src')->nodeValue;
		//$data[$key]['picture_thumb'] = "http://worldskills.org/" . $value;
		$data[$key]['picture_thumb'] = "http://worldskills.org/images/stories/" . $article['images'];

		//$doc->loadHTML($article['fulltext']); // loads your html
		//$xpath = new DOMXPath($doc);
		//$nodelist = $xpath->query("//img"); // find your image
		//$node = $nodelist->item(0); // gets the 1st image
		//$value = $node->attributes->getNamedItem('src')->nodeValue;
		//$data[$key]['picture'] = "http://worldskills.org/" . $value;		
		$data[$key]['picture'] = "http://worldskills.org/images/stories/" . $article['images'];		


		//strip \r\n
		$data[$key]['text_short'] = str_replace("\r\n", "", $data[$key]['text_short']);
		$data[$key]['text_full'] = str_replace("\r\n", "", $data[$key]['text_full']);

		//truncate text

		$truncated_text = explode(" ", $data[$key]['text_short']);
		if(count($truncated_text) > 50){
			$data[$key]['text_short'] = "";

			for($i = 0 ; $i < 50 ; $i++){
				$data[$key]['text_short'] .= $truncated_text[$i] . " ";
			}
			$data[$key]['text_short'] .= "...";
		}//if

	}

	$vupdate = false;
	if($LAST_UPDATED < $NEWS_UPDATED || isset($_REQUEST['f'])){
		$update_date = date("Y-m-d H:i:s");
		writeGzip($FILE, $data, $update_date);
		$vupdate = true;
	}//update file

?>
	<?php if (isset($_REQUEST['v'])): ?>
	<?php if($vupdate): ?>
	<p>File updated, Gzip created, <a href='aggregator/cache/<?php echo $FILE; ?>'>download</a></p>
	<?php else: ?>
	<p>File NOT updated because of no changes, <a href='aggregator/cache/<?php echo $FILE; ?>'>download gzip</a><br />
		<a href='index.php?force=data-news.php'>Force update</a></p>
	<?php endif; ?>
		
	<h1>News Articles</h1>
	<pre>
		<?php echo json_encode($data); ?>
	</pre>
	<?php endif; ?>