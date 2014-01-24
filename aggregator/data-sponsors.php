<?php error_reporting(E_ALL); require_once("setup.inc.php"); ?>

<?php 
	$db = getConnection();

	$FILE = 'data-sponsors.json.gz';
	$LAST_UPDATED = getLastUpdated($FILE);
	$lastmod = '0000-00-00-00 00:00:00';

	$sponsor_information = array(
		"baseColor" => "#047dce",
    	"labelColor" => "#034f83",
    	"sponsors" => array()
		);

	$sql = "SELECT s.id, s.group_id, s.name, s.logo_thumb_url, s.profile_image_url, s.profile_title_en, s.profile_title_de, s.profile_description_en, s.profile_description_de, s.profile_video_url, s.read_more_url, s.created, s.modified FROM app_sponsors s ORDER BY s.name ASC;";
	$res_sponsors = $db->run($sql);

	$sql = "SELECT id, title_en FROM app_sponsor_groups ORDER BY ordernum ASC;";
	$res_groups = $db->run($sql);


	foreach($res_groups as $rg){
		$tmp_sponsors = array();

		foreach($res_sponsors as $rs){
			if($rs['group_id'] != $rg['id']) continue; //skip incorrect groups

			$profile_image = ($rs['profile_image_url'] != '') ? 'http://aggregator.worldskills.org/assets/xcrud/uploaded_images/' . $rs['profile_image_url'] : "";
			
			$tmp_sponsors[] = array('id' => $rs['id'],
				'name' => $rs['name'],
				'logo_thumb_url' => 'http://aggregator.worldskills.org/assets/xcrud/uploaded_images/' . $rs['logo_thumb_url'],
				'profile_image_url' => $profile_image,
				'profile_title' => array('localized_content' => array(
					'EN' => $rs['profile_title_en'],
					'DE' => $rs['profile_title_de']
					)),
				'profile_description' => array('localized_content' => array(
					'EN' => clean_for_json($rs['profile_description_en'], false),
					'DE' => clean_for_json($rs['profile_description_de'], false)
					)),
				'profile_video_url' => $rs['profile_video_url'],
				'read_more_url' => $rs['read_more_url'],
				'created' => $rs['created'],
				'modified' => $rs['modified']
			);		

			$lastmod = ($rs['modified'] > $lastmod) ? $rs['modified'] : $lastmod;		
		}
		

		$sponsor_information['sponsors'][] = array(
			'group' => $rg['title_en'],
			'items' => $tmp_sponsors
			);
	}//foreach



/*
	$sponsor_information = array(
		"baseColor" => "#047dce",
    	"labelColor" => "#034f83",
    	"sponsors" => array(
    		array(
    			'group' => "Global Industry Partners",
    			'items' => array(
					array('id' => "1",
					'name' => 'Autodesk',
					'logo_thumb_url' => 'https://asset1.basecamp.com/1904112/people/1802400-joni-aaltonen/photo/avatar.96.gif',
					'profile_image_url' => 'http://www.worldskills.org/images/stories/header_banners/website-header-restaurant-service.jpg',
					'profile_title' => array('localized_content' => array(
						'EN' => 'Autodesk',
						'DE' => 'Autodesk DE'
						)),
					'profile_description' => array('localized_content' => array(
						'EN' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae, quod blanditiis repellendus pariatur quia consequuntur id eos voluptatum consequatur architecto minima ducimus ipsum sequi! Dolore, maiores beatae magni error reprehenderit.',
						'DE' => 'Lorem Ipsum in german'
						)),
					'profile_video_url' => 'https://www.youtube.com/watch?v=njxSCtE_QG0',
					'read_more_url' => 'http://www.autodesk.com',
					'created' => '0000-00-00 00:00:00',
					'modified' => '0000-00-00 00:00:00'),
		
					array('id' => "2",
					'name' => 'Fluke',
					'logo_thumb_url' => 'https://asset1.basecamp.com/1904112/people/1802400-joni-aaltonen/photo/avatar.96.gif',
					'profile_image_url' => 'http://www.worldskills.org/images/stories/header_banners/website-header-restaurant-service.jpg',
					'profile_title' => array('localized_content' => array(
						'EN' => 'Fluke',
						'DE' => 'Fluke DE'
						)),
					'profile_description' => array('localized_content' => array(
						'EN' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae, quod blanditiis repellendus pariatur quia consequuntur id eos voluptatum consequatur architecto minima ducimus ipsum sequi! Dolore, maiores beatae magni error reprehenderit.',
						'DE' => 'Lorem Ipsum in german'
						)),
					'profile_video_url' => 'https://www.youtube.com/watch?v=njxSCtE_QG0',
					'read_more_url' => 'http://www.fluke.com',
					'created' => '0000-00-00 00:00:00',
					'modified' => '0000-00-00 00:00:00')
				)
			), //gip

			array(
				'group' => "Event Sponsors",
				'items' => array(
					array('id' => "1",
					'name' => 'Autodesk',
					'logo_thumb_url' => 'https://asset1.basecamp.com/1904112/people/1802400-joni-aaltonen/photo/avatar.96.gif',
					'profile_image_url' => 'http://www.worldskills.org/images/stories/header_banners/website-header-restaurant-service.jpg',
					'profile_title' => array('localized_content' => array(
						'EN' => 'Autodesk',
						'DE' => 'Autodesk DE'
						)),
					'profile_description' => array('localized_content' => array(
						'EN' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae, quod blanditiis repellendus pariatur quia consequuntur id eos voluptatum consequatur architecto minima ducimus ipsum sequi! Dolore, maiores beatae magni error reprehenderit.',
						'DE' => 'Lorem Ipsum in german'
						)),
					'profile_video_url' => 'https://www.youtube.com/watch?v=njxSCtE_QG0',
					'read_more_url' => 'http://www.autodesk.com',
					'created' => '0000-00-00 00:00:00',
					'modified' => '0000-00-00 00:00:00'),
		
					array('id' => "2",
					'name' => 'Fluke',
					'logo_thumb_url' => 'https://asset1.basecamp.com/1904112/people/1802400-joni-aaltonen/photo/avatar.96.gif',
					'profile_image_url' => 'http://www.worldskills.org/images/stories/header_banners/website-header-restaurant-service.jpg',
					'profile_title' => array('localized_content' => array(
						'EN' => 'Fluke',
						'DE' => 'Fluke DE'
						)),
					'profile_description' => array('localized_content' => array(
						'EN' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae, quod blanditiis repellendus pariatur quia consequuntur id eos voluptatum consequatur architecto minima ducimus ipsum sequi! Dolore, maiores beatae magni error reprehenderit.',
						'DE' => 'Lorem Ipsum in german'
						)),
					'profile_video_url' => 'https://www.youtube.com/watch?v=njxSCtE_QG0',
					'read_more_url' => 'http://www.fluke.com',
					'created' => '0000-00-00 00:00:00',
					'modified' => '0000-00-00 00:00:00'),
		
					array('id' => "3",
					'name' => 'Samsung',
					'logo_thumb_url' => 'https://asset1.basecamp.com/1904112/people/1802400-joni-aaltonen/photo/avatar.96.gif',
					'profile_image_url' => 'http://www.worldskills.org/images/stories/header_banners/website-header-restaurant-service.jpg',
					'profile_title' => array('localized_content' => array(
						'EN' => 'Samsung',
						'DE' => 'Samsung DE'
						)),
					'profile_description' => array('localized_content' => array(
						'EN' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Recusandae, quod blanditiis repellendus pariatur quia consequuntur id eos voluptatum consequatur architecto minima ducimus ipsum sequi! Dolore, maiores beatae magni error reprehenderit.',
						'DE' => 'Lorem Ipsum in german'
						)),
					'profile_video_url' => 'https://www.youtube.com/watch?v=njxSCtE_QG0',
					'read_more_url' => 'http://www.Samsung.com',
					'created' => '0000-00-00 00:00:00',
					'modified' => '0000-00-00 00:00:00')	
				)//items
			)//event sponsors
		)//sponsors
	);//end array*/


	$vupdate = false;
	if($LAST_UPDATED < $lastmod || isset($_REQUEST['f'])){
		$update_date = $lastmod;
		writeGzip($FILE, $sponsor_information, $update_date);
		$vupdate = true;
	}//update file

?>
	<?php if (isset($_REQUEST['v'])): ?>
	<?php if($vupdate): ?>
	<p>File updated, Gzip created, <a href='aggregator/cache/<?php echo $FILE; ?>'>download</a></p>
	<?php else: ?>
	<p>File NOT updated because of no changes, <a href='aggregator/cache/<?php echo $FILE; ?>'>download gzip</a><br /><br />
		<a class='btn btn-warning' href='index.php?force=data-sponsors.php'><i class='icon-white icon-refresh'></i>&nbsp;Force update</a></p>
	<?php endif; ?>
		
	<h1>Sponsors</h1>
	<p><a class='btn btn-info' style='float: left;' href='index.php?edit=sponsors'><i class='icon-white icon-edit'></i>&nbsp;Edit Sponsor Information</a>
	<a class='btn btn-info' style='float: left; margin-left: 6px;' href='index.php?edit=sponsor_groups'><i class='icon-white icon-edit'></i>&nbsp;Edit Sponsor Groups</a></p>
	<br style='clear: both;' /><br />
	<pre>
		<?php echo json_encode($sponsor_information); ?>
	</pre>
	<?php endif; ?>