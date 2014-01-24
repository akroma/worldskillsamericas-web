<?php error_reporting(E_ALL); require_once("setup.inc.php"); ?>

<?php
	$db = getConnection();

	$SKILL_NUMBER = $_REQUEST['skill_number'];
	$COMPETITION_ID = COMPETITION_ID;

	$FILE = 'data-skills-' . $SKILL_NUMBER . '.json.gz';
	$LAST_UPDATED = getLastUpdated($FILE);


	/******************* SKILL NAMES LOCALIZED *******************/	

	$skill_sql = "SELECT ct.id as 'ctid', it.id as 'itid', ct.competition_trade_id, it.name_en, it.name_de, it.name_fr, sd.required_skills_en, sd.required_skills_de, sd.industry_action_en, sd.industry_action_de, sd.competition_action_en, sd.competition_action_de, sd.modified as 'sd_modified', cts.sector_name, cts.sector_name_de, cts.sector_name_fr, cts.sector_baseColor, cts.sector_secondaryColor, cts.sector_labelColor, ct.modified as 'ct_modified', it.modified as 'it_modified' FROM competition_trades ct LEFT JOIN internal_trades it ON it.id = ct.internal_trade_id LEFT JOIN competition_trade_sectors cts ON cts.id = ct.host_sector_id LEFT JOIN app_skill_definitions sd ON sd.internal_trade_id = it.id
	WHERE ct.competition_id = {$COMPETITION_ID} ORDER BY ct.competition_trade_id ASC;";

	$skill_res = $db->run($skill_sql);
	$skill_array = array();

	$FULL_DATA = array();

	$colors = array();

	$skill_ct_modified = array();
	$skill_it_modified = array();
	$skill_sd_modified = array();

	foreach($skill_res as $key=>$val){
		//if($val['competition_trade_id'] != $SKILL_NUMBER) continue; //get out of the loop early

		$skill_names[$val['competition_trade_id']]['localized_content'] = array(
				'EN' => $val['name_en'], 
				'DE' => $val['name_de'], 
				'FR' => $val['name_fr']
				);

		$skill_categories[$val['competition_trade_id']]['localized_content'] = array(
				'EN' => $val['sector_name'], 
				'DE' => $val['sector_name_de'], 
				'FR' => $val['sector_name_fr']
				);	

		$description_de = (strlen($val['required_skills_de']) > 0 || strlen($val['industry_action_de']) > 0 || strlen($val['competition_action_de']) > 0) ? "<h2>Required Skills</h2><p>" . clean_for_json($val['required_skills_de'], false) . "</p><h2>Industry Action</h2><p>" . clean_for_json($val['industry_action_de'], false) . "</p>" : "";
		$skill_descriptions[$val['competition_trade_id']]['localized_content'] = array(
				'EN' => "<h2>Required Skills</h2><p>" . clean_for_json($val['required_skills_en'], false) . "</p><h2>Industry Action</h2><p>" . clean_for_json($val['industry_action_en'], false) . "</p>",
				'DE' => $description_de
				);	

		//colors
		$colors[$val['competition_trade_id']]['baseColor'] = $val['sector_baseColor'];
		$colors[$val['competition_trade_id']]['secondaryColor'] = $val['sector_secondaryColor'];
		$colors[$val['competition_trade_id']]['labelColor'] = $val['sector_labelColor'];

		$skill_ids[$val['competition_trade_id']] = $val['itid'];
		$skill_ct_modified[$val['competition_trade_id']] = $val['ct_modified'];
		$skill_it_modified[$val['competition_trade_id']] = $val['it_modified'];
		$skill_sd_modified[$val['competition_trade_id']] = $val['sd_modified'];
	}//skills


	$skill_modified = ($skill_ct_modified[$SKILL_NUMBER] < $skill_it_modified[$SKILL_NUMBER]) ? $skill_it_modified[$SKILL_NUMBER] : $skill_ct_modified[$SKILL_NUMBER];
	$skill_modified = ($skill_modified > $skill_sd_modified[$SKILL_NUMBER]) ? $skill_modified : $skill_sd_modified[$SKILL_NUMBER];

	$FULL_DATA[$SKILL_NUMBER] = array(
		'id' => $skill_ids[$SKILL_NUMBER],
		'number' => $SKILL_NUMBER,
		'baseColor' => $colors[$SKILL_NUMBER]['baseColor'],
		'secondaryColor' => $colors[$SKILL_NUMBER]['secondaryColor'],
		'labelColor' => $colors[$SKILL_NUMBER]['labelColor'],
		'name' => $skill_names[$SKILL_NUMBER],
		'category' => $skill_categories[$SKILL_NUMBER],
		'description_title' => array('EN' => 'Skill Explained', 'DE' => 'Explained', 'FR' => 'Skill Explained'),
		'description' => $skill_descriptions[$SKILL_NUMBER],
		'images' => getSkillImages($SKILL_NUMBER),
		'people' => array(),
		//'sponsors' => getSkillSponsors($SKILL_NUMBER),
		'test_project' => getTestProject($SKILL_NUMBER),
		'event_schedule' => getEventSchedule($skill_ids[$SKILL_NUMBER]),
		'modified' => $skill_modified
		);	

	/******************* PROFILES *******************/

	$PEOPLE = array();

	//competitors
	$C_SQL = <<< SQL
SELECT rc.personnel_id AS 'id',
'C' AS 'type',
CONCAT('http://wiw.worldskills.org/wiw/img/wiw/resized/', p.image_name) AS 'picture_thumb_url',
CONCAT('http://wiw.worldskills.org/wiw/img/wiw/accreditation/', p.image_name) AS 'picture_url',
p.first_name AS 'first_name',
p.last_name AS 'last_name',
rego.country_abbreviation AS 'country_abbreviation',
c.name AS 'country',
ct.competition_trade_id AS 'skill_number',
it.name_en AS 'skill_name',
cts.sector_name as 'skill_category',
'Profile Bio' as 'profile_title',
pp.description as 'profile_bio',
pp.facebook as 'facebook',
pp.googleplus as 'googleplus',
pp.twitter as 'twitter',
pp.linkedin as 'linkedin',
p.created as 'created',
p.modified as 'modified',
rc.modified as 'modified_rego'

FROM registration_competitors rc
LEFT JOIN personnel p ON p.id = rc.personnel_id

LEFT JOIN registrations rego ON rego.id = rc.registration_id
LEFT JOIN countries c ON c.abbreviation = rego.country_abbreviation

LEFT JOIN competition_trades ct ON rc.competition_trade_id = ct.competition_trade_id
LEFT JOIN internal_trades it ON it.id = ct.internal_trade_id
LEFT JOIN competition_trade_sectors cts ON cts.id = ct.host_sector_id
LEFT JOIN personnel_profiles pp ON pp.personnel_id = p.id
WHERE rc.registration_id IN (SELECT id FROM registrations 
WHERE competition_id = {$COMPETITION_ID}
AND ct.competition_id = {$COMPETITION_ID}
AND ct.competition_trade_id = '{$SKILL_NUMBER}'
AND rego.country_abbreviation != 'ZZ'
);
SQL;


	//experts, including CE/DCE
$E_SQL = <<< SQL
SELECT re.personnel_id AS 'id',
'E' AS 'type',
CONCAT('http://wiw.worldskills.org/wiw/img/wiw/resized/', p.image_name) AS 'picture_thumb_url',
CONCAT('http://wiw.worldskills.org/wiw/img/wiw/accreditation/', p.image_name) AS 'picture_url',
p.first_name AS 'first_name',
p.last_name AS 'last_name',
rego.country_abbreviation AS 'country_abbreviation',
c.name AS 'country',
ct.competition_trade_id AS 'skill_number',
it.name_en AS 'skill_name',
cts.sector_name as 'skill_category',
'Profile Bio' as 'profile_title',
pp.description as 'profile_bio',
pp.facebook as 'facebook',
pp.googleplus as 'googleplus',
pp.twitter as 'twitter',
pp.linkedin as 'linkedin',
p.created as 'created',
p.modified as 'modified',
re.modified as 'modified_rego'

FROM registration_experts re
LEFT JOIN personnel p ON p.id = re.personnel_id
LEFT JOIN personnel_contacts pc ON p.id = pc.personnel_id

LEFT JOIN registrations rego ON rego.id = re.registration_id
LEFT JOIN countries c ON c.abbreviation = rego.country_abbreviation

LEFT JOIN competition_trades ct ON re.competition_trade_id = ct.competition_trade_id
LEFT JOIN internal_trades it ON it.id = ct.internal_trade_id
LEFT JOIN competition_trade_sectors cts ON cts.id = ct.host_sector_id
LEFT JOIN personnel_profiles pp ON pp.personnel_id = p.id
WHERE re.registration_id IN (SELECT id FROM registrations 
WHERE competition_id = {$COMPETITION_ID}
AND ct.competition_id = {$COMPETITION_ID}
AND ct.competition_trade_id = '{$SKILL_NUMBER}'
AND rego.country_abbreviation != 'ZZ'
);

SQL;

$itid = $skill_ids[$SKILL_NUMBER];

$OTHER_SQL = <<< SQL
SELECT p.id AS 'id',
UCASE(pr.name) AS 'type',
CONCAT('http://wiw.worldskills.org/wiw/img/wiw/resized/', p.image_name) AS 'picture_thumb_url',
CONCAT('http://wiw.worldskills.org/wiw/img/wiw/accreditation/', p.image_name) AS 'picture_url',
p.first_name AS 'first_name',
p.last_name AS 'last_name',
pc.country_abbreviation AS 'country_abbreviation',
c.name AS 'country',
ct.competition_trade_id AS 'skill_number',
it.name_en AS 'skill_name',
cts.sector_name as 'skill_category',
'Profile Bio' as 'profile_title',
pp.description as 'profile_bio',
pp.facebook as 'facebook',
pp.googleplus as 'googleplus',
pp.twitter as 'twitter',
pp.linkedin as 'linkedin',
p.created as 'created',
p.modified as 'modified',
prl.value1 as 'raw_skill'

FROM personnel_relation_links prl
LEFT JOIN personnel p ON p.id = prl.personnel_id
LEFT JOIN personnel_contacts pc ON p.id = pc.personnel_id
LEFT JOIN countries c ON c.abbreviation = pc.country_abbreviation
LEFT JOIN internal_trades it ON it.id = prl.value1
LEFT JOIN competition_trades ct ON ct.internal_trade_id = it.id
LEFT JOIN competition_trade_sectors cts ON cts.id = ct.host_sector_id
LEFT JOIN personnel_profiles pp ON pp.personnel_id = p.id
LEFT JOIN personnel_relations pr ON pr.id = prl.relation_id
WHERE prl.relation_id IN (19, 10, 11) AND prl.end IS NULL AND prl.value2 = {$COMPETITION_ID}
AND ct.competition_id = {$COMPETITION_ID}
AND pc.country_abbreviation != 'ZZ'
AND (prl.value1 = '{$itid}' OR prl.value1 LIKE '%;{$itid};%' OR prl.value1 LIKE '%;{$itid}' OR prl.value1 LIKE '{$itid};%');

SQL;



	$C_RES = $db->run($C_SQL);

	//fix TBA for competitors
	foreach($C_RES as $key=>$val){

		//create mobile images
		//$tmp_image = basename($val['picture_url']);
		//$croppedDir = "/var/www/vhosts/worldskills.org/cake/wiw/webroot/img/wiw/cropped/";
		//$mobileDir = "/var/www/vhosts/worldskills.org/cake/wiw/webroot/img/wiw/mobile/";
		//system("convert -resize 240x296 {$croppedDir}{$_POST[original_name]} {$mobileDir}{$_POST[original_name]} 2>&1 &");

		if($val['id'] == 0){
			$C_RES[$key]['first_name'] = "TBA";
			$C_RES[$key]['last_name'] = "TBA";
		}//if

		if(empty($val['modified'])){
			$C_RES[$key]['modified'] = $val['modified_rego'];
		}
		
		//check and update modified
		if($FULL_DATA[$SKILL_NUMBER]['modified'] < $C_RES[$key]['modified']){
			$FULL_DATA[$SKILL_NUMBER]['modified'] = $C_RES[$key]['modified'];
		}//

		$C_RES[$key]['profile_bio'] = nl2br($val['profile_bio']);

		unset($C_RES[$key]['modified_rego']);
	}//foreach competitors

	$FULL_DATA[$SKILL_NUMBER]['people']['competitors'] = $C_RES;
		
	$E_RES = $db->run($E_SQL);
	//fetch CE and DCE positions

	$CHIEFS_SQL = "SELECT personnel_id, relation_id FROM personnel_relation_links WHERE relation_id IN (7, 8) AND end is null AND value2 = {$COMPETITION_ID};";
	$CHIEFS_RES = $db->run($CHIEFS_SQL);

	foreach($CHIEFS_RES as $chief){
		$found = false;

		$chief['personnel_id'];

		foreach($E_RES as $key=>$val){
			if($val['id'] == $chief['personnel_id']){
				$found = true;
				$E_RES[$key]['type'] = ($chief['relation_id'] == 7) ? "CE" : "DCE";				
			}

			if($found) break;
		}
	}

	//fix TBA for competitors
	foreach($E_RES as $key=>$val){
		if($val['id'] == 0){
			$E_RES[$key]['first_name'] = "TBA";
			$E_RES[$key]['last_name'] = "TBA";
		}//if


		if(empty($val['modified'])){
			$E_RES[$key]['modified'] = $val['modified_rego'];
		}

		//check and update modified
		if($FULL_DATA[$SKILL_NUMBER]['modified'] < $E_RES[$key]['modified']){
			$FULL_DATA[$SKILL_NUMBER]['modified'] = $E_RES[$key]['modified'];
		}//

		$E_RES[$key]['profile_bio'] = nl2br($val['profile_bio']);

		unset($E_RES[$key]['modified_rego']);		
	}//foreach competitors	

	$FULL_DATA[$SKILL_NUMBER]['people']['experts'] = $E_RES;

	//WSS, WSSA, JP
	$OTHER_RES = $db->run($OTHER_SQL);

	//fix types for WSS AND WSSA and multiple skills
	foreach($OTHER_RES as $key=>$val){
		if($val['type'] == 'SM') $OTHER_RES[$key]['type'] = 'WSS';
		if($val['type'] == 'SMA') $OTHER_RES[$key]['type'] = 'WSSA';

		if(strstr($val['raw_skill'], ";")){
			$skills = explode(";", $val['raw_skill']);

			foreach($skills as $skill){
				$sql_temp = "SELECT ct.competition_trade_id, it.name_en FROM internal_trades it LEFT JOIN competition_trades ct ON ct.internal_trade_id = it.id WHERE ct.competition_id = {$COMPETITION_ID} AND it.id = {$skill};";
				$res_temp = $db->run($sql_temp);
					
				$OTHER_RES[$key]['multiple_skills'][] = array('skill_number' => $res_temp[0]['competition_trade_id'], 'skill_name' => $res_temp[0]['name_en']);
			}
		}//multiple skills

		unset($OTHER_RES[$key]['raw_skill']);

		$OTHER_RES[$key]['profile_bio'] = nl2br($val['profile_bio']);

		//check and update modified
		if($FULL_DATA[$SKILL_NUMBER]['modified'] < $OTHER_RES[$key]['modified']){
			$FULL_DATA[$SKILL_NUMBER]['modified'] = $OTHER_RES[$key]['modified'];
		}//
	}//other
	$FULL_DATA[$SKILL_NUMBER]['people']['others'] = $OTHER_RES;

	$types = array('competitors', 'experts', 'others');
	foreach($types as $type){
		foreach($FULL_DATA[$SKILL_NUMBER]['people'][$type] as $key=>$val){
			
			//localization for skill names and categories
			$FULL_DATA[$SKILL_NUMBER]['people'][$type][$key]['skill_name'] = $skill_names[$val['skill_number']];
			$FULL_DATA[$SKILL_NUMBER]['people'][$type][$key]['skill_category'] = $skill_categories[$val['skill_number']];

			//encode images to base64
			//$FULL_DATA['profiles'][$type][$key]['image_base64_thumb'] = base64_encode(file_get_contents($val['picture_url_thumb']));
	 		//$FULL_DATA['profiles'][$type][$key]['image_base64'] = base64_encode(file_get_contents($val['picture_url']));

		}//foreach
	}//types
	 
	$vupdate = false;
	if($FULL_DATA[$SKILL_NUMBER]['modified'] > $LAST_UPDATED || isset($_REQUEST['f'])){
		$update_date = $FULL_DATA[$SKILL_NUMBER]['modified'];
		writeGzip($FILE, $FULL_DATA[$SKILL_NUMBER], $update_date);
		$vupdate = true;
	}//update file

?>
	<?php if (isset($_REQUEST['v'])): ?>
	<?php if($vupdate): ?>
	<p>File updated, Gzip created, <a href='aggregator/cache/<?php echo $FILE; ?>'>download</a></p>
	<?php else: ?>
	<p>File NOT updated because of no changes, <a href='aggregator/cache/<?php echo $FILE; ?>'>download gzip</a>
		<!--<a href='index.php?force=data-skill-specific.php'>Force update</a></p>-->
	<?php endif; ?>

	<p><a style='float: left;' class='btn btn-info' href='index.php?edit=skill_descriptions'><i class='icon-white icon-refresh'></i>&nbsp;Edit Skill Descriptions</a>
	<a style='float: left; margin-left: 6px;' class='btn btn-info' href='index.php?edit=test_projects'><i class='icon-white icon-refresh'></i>&nbsp;Edit Test Projects</a>
	<a style='float: left; margin-left: 6px;' class='btn btn-info' href='index.php?edit=skill_sponsors'><i class='icon-white icon-refresh'></i>&nbsp;Edit Skill Sponsors</a>
	<a style='float: left; margin-left: 6px;' class='btn btn-info' href='index.php?edit=skill_highlights'><i class='icon-white icon-refresh'></i>&nbsp;Edit Highlight Pictures</a></p>
	<br style='clear: both;' />

	<p style='margin-top: 2em; margin-bottom: 1em;'>
		<b>Force update on skill:</b> <br />
		<?php 
			foreach($skill_ids as $key=>$val){
				echo "<a class='btn btn-small btn-info' style='float: left; margin-left: 2px; margin-bottom: 2px;' href='index.php?force=data-skill-specific.php?skill_number={$key}'>".$key. "</a>";
			}
		?>
		<br style='clear: both;' />
	</p>
		
	<h1>Profile data review</h1>
	<h2><?php echo $FULL_DATA[$SKILL_NUMBER]['category']['localized_content']['EN'] . " - " . $FULL_DATA[$SKILL_NUMBER]['number'] . ", " . $FULL_DATA[$SKILL_NUMBER]['name']['localized_content']['EN']; ?></h2>

		<?php foreach ($types as $type): ?>
			<h2><?php echo strtoupper($type); ?></h2>
			<table class='table table-striped'>
			<thead>
				<tr>
					<th>id</th>
					<th>type</th>
					<!--<th>picture_url_thumb</th>-->
					<!--<th>picture_url</th>-->
					<th>first_name</th>
					<th>last_name</th>
					<th>country_abbreviation</th>
					<th>country</th>
					<th>skill_number</th>
					<th>skill_name</th>
					<th>skill_category</th>
					<th>profile_bio</th>
					<th>facebook</th>
					<th>googleplus</th>
					<th>twitter</th>
					<th>linkedin</th>
					<th>created</th>
					<th>modified</th>
				</tr>
			</thead>
			<tbody>
<?php foreach ($FULL_DATA[$SKILL_NUMBER]['people'][$type] as $key => $value): ?>
				<tr>
					<td><?php echo $value['id']; ?></td>
					<td><?php echo $value['type']; ?></td>
					<!--<td><img src='<?php echo $value['picture_url_thumb']; ?>' alt='<?php echo $value['picture_url_thumb']; ?>' style='width: 90px; max-height: 90px;'/></td>-->
					<!--<td><?php echo $value['picture_url']; ?></td>-->
					<td><?php echo $value['first_name']; ?></td>
					<td><?php echo $value['last_name']; ?></td>
					<td><?php echo $value['country_abbreviation']; ?></td>
					<td><?php echo $value['country']; ?></td>
					<td><?php echo $value['skill_number']; ?></td>
					<td><?php print_r($value['skill_name']); ?></td>
					<td><?php print_r($value['skill_category']); ?></td>
					<td><?php echo $value['profile_bio']; ?></td>
					<td><?php echo $value['facebook']; ?></td>
					<td><?php echo $value['googleplus']; ?></td>
					<td><?php echo $value['twitter']; ?></td>
					<td><?php echo $value['linkedin']; ?></td>
					<td><?php echo $value['created']; ?></td>
					<td><?php echo $value['modified']; ?></td>
				</tr>
		<?php endforeach ?>				
			</tbody>
		</table>
		<?php endforeach ?>
	<?php endif ?>
		
