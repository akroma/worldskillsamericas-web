<?php 
	require("setup.inc.php"); 

	if(!isset($_REQUEST) || empty($_REQUEST) || !isset($_REQUEST['feed']) || !isset($_REQUEST['timestamp'])) exit("500|No POST data");

	$compression = false;
	if(isset($_REQUEST['compression'])){
		ob_start('ob_gzhandler');
		$compression = true;
	}

	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
	header("Content-Type: application/json");
	if($compression)
		header("Content-Encoding: gzip");	

	$feed = $_REQUEST['feed'];
	$timestamp = $_REQUEST['timestamp'];
	$file = false;
	$json = "Use gzip file";

	if(isset($_REQUEST['skill'])) $skill = $_REQUEST['skill'];
	else $skill = 'N/A';

	$db = getConnection();
	$generic = "'data-event.json.gz', 'data-sectors.json.gz', 'data-sponsors.json.gz', 'data-venue.json.gz'";

	switch($feed){
		case 'full':
			//$sql = "SELECT last_updated FROM app_updates ORDER BY last_updated DESC;";
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-full.json.gz';";
			//$file = "aggregator/cache_collections/full-data.tar.gz";
			$file = "aggregator/cache/data-full.json.gz";
			$json = $file;
		break;
		case 'generic':
			//$sql = "SELECT last_updated FROM app_updates WHERE file IN ({$generic}) ORDER BY last_updated DESC;";
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-generic.json.gz';";
			//$file = "aggregator/cache_collections/generic.tar.gz";
			$file = "aggregator/cache/generic.json.gz";
			$json = $file;
		break;
		case 'event':
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-event.json.gz';";
			$file = "aggregator/cache/data-event.json.gz";
			$json = $file;
		break;
		case 'event-schedule':
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-event-schedule.json.gz';";
			$file = "aggregator/cache/data-event-schedule.json.gz";
			$json = $file;
		break;		
		case 'sectors':
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-sectors.json.gz';";
			$file = "aggregator/cache/data-sectors.json.gz";
			$json = $file;
		break;
		case 'sponsors':
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-sponsors.json.gz';";
			$file = "aggregator/cache/data-sponsors.json.gz";
			$json = $file;
		break;
		case 'venue':
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-venue.json.gz';";
			$file = "aggregator/cache/data-venue.json.gz";
			$json = $file;
		break;
		case 'news':
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-news.json.gz';";
			$file = "aggregator/cache/data-news.json.gz";
			$json = $file;
		break;
		case 'local-push':
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-local-push.json.gz';";
			$file = "aggregator/cache/data-local-push.json.gz";
			$json = $file;
		break;
		case 'photos':
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-photos.json.gz';";
			$file = "aggregator/cache/data-photos.json.gz";
			$json = $file;
		break;
		case 'feedback':
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-feedback.json.gz';";
			$file = "aggregator/cache/data-feedback.json.gz";
			$json = $file;			
		break;
		case 'uploads':
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-uploads.json.gz';";
			$file = "aggregator/cache/data-uploads.json.gz";
			$json = $file;	
		break;		
		case 'videos':
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-videos.json.gz';";
			$file = "aggregator/cache/data-videos.json.gz";
			$json = $file;			
		break;
		case 'all-skills':
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-all-skills.json.gz';";
			//$sql = "SELECT last_updated FROM app_updates WHERE file like 'data-skills-%' ORDER BY last_updated DESC;";
			//$file = "aggregator/cache_collections/all-skills.tar.gz";
			$file = "aggregator/cache/data-all-skills.json.gz";
			$json = $file;
		break;
		case 'skill':
			$sql = "SELECT last_updated FROM app_updates WHERE file = 'data-skills-{$skill}.json.gz';";
			$file = "aggregator/cache/data-skills-{$skill}.json.gz";
			$json = $file;
		break;
		default:
			$sql = "SELECT last_updated FROM app_updates WHERE file = '{$feed} ORDER BY last_updated DESC;";
			$file = "aggregator/cache/{$feed}";
			$json = $file;
		break;
	}//switch

	$res_updated = $db->run($sql);
	$last_updated = false;

	if($res_updated !== false && count($res_updated > 0)){
		$last_updated = $res_updated[0]['last_updated'];
	}//result
	else
		$last_updated = $_REQUEST['timestamp'];

	$json_code = "Use gzip file";
	if($json != "Use gzip file"){
		$json_code = json_decode(file_get_contents(substr($json, 11, -3)));
		$json = substr($json, 0, -3);
	}

	if($last_updated > $timestamp){
		echo json_encode(array(
			'file' => SITE_URL . $file,		
			'file_json' => SITE_URL . $json,
			'modified' => $last_updated,
			'feed_content' => $json_code
			));
	}
	else{		
		echo json_encode(array('modified' => $last_updated));

	}

	//log call
	$sql = "INSERT INTO app_api_stats VALUES('', '{$feed}', '{$timestamp}', '{$skill}', NOW(), NOW());";
	$db->run($sql);
	

?>