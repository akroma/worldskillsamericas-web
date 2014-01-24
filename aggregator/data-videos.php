<?php error_reporting(E_ALL); require_once("setup.inc.php"); ?>

<?php 
	$db = getConnection();

	$FILE = 'data-videos.json.gz';
	$LAST_UPDATED = getLastUpdated($FILE);


/*"date": '12/08/2012',
    	"title": 'LOREM IPSM DOLOR SIT AMET',
        "text": 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit ess',
        "img": "./resources/images/layout-markup/video-thumb.png",
        "m4v": './resources/videos/BigBuck.m4v',
        "webm": './resources/videos/BigBuck.webm'*/

        $playlist_id = 'PLaeX00IR4g1tio0yO-Lnf2ZtUM9vL1rQT';
		$url = "https://gdata.youtube.com/feeds/api/playlists/".$playlist_id."?v=2&alt=json";
		$data = json_decode(file_get_contents($url),true);
		$info = $data["feed"];
		$video = $info["entry"];
		$nVideo = count($video);


		$videos = array();

		foreach($video as $v){
			$modified = substr($v['published']['$t'], 0, -5);		
			$modified = explode("T", $modified);
			$modified = $modified[0] . " " . $modified[1];

			$videos[] = array(
				'title' => $v['title']['$t'],
				'text' => $v['media$group']['media$description']['$t'],
				'img' => $v['media$group']['media$thumbnail'][0]['url'],
				'modified' => $modified,
				'video_url' => 'http://www.youtube.com/embed/' . $v['media$group']['yt$videoid']['$t']
				);
			
		}
		//echo "<pre>";
		//var_dump($videos);
		foreach($videos as $key=>$val){
			$date[$key] = $val['modified'];
		}
		array_multisort($date, SORT_DESC, $videos);
		//echo "<hr>";
		//var_dump($videos);

		/*'WorldSkills Leipzig 2013' => 'http://www.youtube.com/playlist?list=PLaeX00IR4g1tio0yO-Lnf2ZtUM9vL1rQT',
		'Featured Videos' => 'http://www.youtube.com/playlist?list=PLaeX00IR4g1u_kTQbDeRm6slAphvFqP79',
		'WorldSkills London 2011' => 'http://www.youtube.com/playlist?list=PL6B1936E4EB92ACD6',
		'WSLondon 2011 Commemorative DVD' => 'http://www.youtube.com/playlist?list=PL9F1742430644EA03',
		'WorldSkills Calgary 2009 - Final Report' => 'http://www.youtube.com/playlist?list=PL61435247672EB13B',
		'Skills: Social & Personal Services' => 'http://www.youtube.com/playlist?list=PL4819E52AB66AAD48',
		'Skills: Creative Arts & Fashion' => 'http://www.youtube.com/playlist?list=PLF9312688A653EE94',		
		'Skills: Information & Communication Technology' => 'http://www.youtube.com/playlist?list=PLE3EF87E92CD546AD',		
		'Skills: Manufacturing (& Engineering) Technology' => 'http://www.youtube.com/playlist?list=PLAB3794DF5D80352B',		
		'Skills: Construction & Building Technology' => 'http://www.youtube.com/playlist?list=PLFA0F70C61D65590A',		
		'Skills: Transportation & Logistics' => 'http://www.youtube.com/playlist?list=PL9BE2836161D6A014'		
		);*/

	$vupdate = false;
	if(true){ //always update
		$update_date = date("Y-m-d H:i:s");
		writeGzip($FILE, $videos, $update_date);
		$vupdate = true;
	}//update file

?>
	<?php if (isset($_REQUEST['v'])): ?>
	<?php if($vupdate): ?>
	<p>File updated, Gzip created, <a href='aggregator/cache/<?php echo $FILE; ?>'>download</a></p>
	<?php else: ?>
	<p>File NOT updated because of no changes, <a href='aggregator/cache/<?php echo $FILE; ?>'>download gzip</a><br /><br />
		<a class='btn btn-warning' href='index.php?force=data-videos.php'><i class='icon-white icon-refresh'></i>&nbsp;Force update</a></p>
	<?php endif; ?>
		
	<h1>Videos</h1>
	<p><a class='btn btn-info' href='index.php?edit=videos'><i class='icon-white icon-edit'></i>&nbsp;Edit Youtube Feeds</a></p>
	<pre>
		<?php echo json_encode($videos); ?>
	</pre>	
	<?php endif; ?>