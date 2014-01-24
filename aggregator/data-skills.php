<?php require_once("setup.inc.php"); ?>

<?php 
	$db = getConnection();

	$SKILL_NUMBER = false;
	$COMPETITION_ID = COMPETITION_ID;

	/******************* SKILL NAMES LOCALIZED *******************/	

	$skill_sql = "SELECT ct.id as 'ctid', it.id as 'itid', ct.competition_trade_id, it.name_en, it.name_de, it.name_fr, it.description_en, it.description_de, it.description_fr, cts.sector_name, cts.sector_name_de, cts.sector_name_fr, ct.modified as 'ct_modified', it.modified as 'it_modified' FROM competition_trades ct LEFT JOIN internal_trades it ON it.id = ct.internal_trade_id LEFT JOIN competition_trade_sectors cts ON cts.id = ct.host_sector_id 
	WHERE ct.competition_id = {$COMPETITION_ID} ORDER BY ct.competition_trade_id ASC;";

	$skill_res = $db->run($skill_sql);
	$skill_array = array();

	$FULL_DATA = array();

	foreach($skill_res as $key=>$val){
		$SKILL_NUMBER = $val['competition_trade_id'];

		$skill_names[$SKILL_NUMBER]['localized_content'] = array(
				'EN' => $val['name_en'], 
				'DE' => $val['name_de'], 
				'FR' => $val['name_fr']
				);

		$skill_categories[$SKILL_NUMBER]['localized_content'] = array(
				'EN' => $val['sector_name'], 
				'DE' => $val['sector_name_de'], 
				'FR' => $val['sector_name_fr']
				);	

		$skill_descriptions[$SKILL_NUMBER]['localized_content'] = array(
				'EN' => $val['description_en'], 
				'DE' => $val['description_de'], 
				'FR' => $val['description_fr']
				);	

		$skill_ids[$SKILL_NUMBER] = $val['itid'];

		$skill_modified = ($val['ct_modified'] < $val['it_modified']) ? $val['it_modified'] : $val['ct_modified'];

		$FULL_DATA[$SKILL_NUMBER] = array(
			'id' => $skill_ids[$SKILL_NUMBER],
			'number' => $SKILL_NUMBER,
			'name' => $skill_names[$SKILL_NUMBER],
			'category' => $skill_categories[$SKILL_NUMBER],
			'description_title' => array('EN' => 'Skill Explained', 'DE' => 'Explained', 'FR' => 'Skill Explained'),
			'description' => $skill_descriptions[$SKILL_NUMBER],
			'images' => getSkillImages($SKILL_NUMBER),
			'people' => array(),
			'sponsors' => getSkillSponsors($SKILL_NUMBER),
			'test_project' => getTestProject($SKILL_NUMBER),
			'modified' => $skill_modified
			);			
	}//skills

	/******************* PROFILES *******************/	

	//competitors
	$C_SQL = <<< SQL
SELECT rc.personnel_id AS 'id',
'C' AS 'type',
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
);
SQL;

	//experts, including CE/DCE
$E_SQL = <<< SQL
SELECT re.personnel_id AS 'id',
'E' AS 'type',
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
);

SQL;

$itid = $skill_ids[$SKILL_NUMBER];

$OTHER_SQL = <<< SQL
SELECT p.id AS 'id',
UCASE(pr.name) AS 'type',
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

SQL;



	$C_RES = $db->run($C_SQL);

	//fix TBA for competitors
	foreach($C_RES as $key=>$val){
		if($val['id'] == 0){
			$C_RES[$key]['first_name'] = "TBA";
			$C_RES[$key]['last_name'] = "TBA";
		}//if

		if(empty($val['modified'])){
			$C_RES[$key]['modified'] = $val['modified_rego'];
		}

		//check and update modified
		if($FULL_DATA[$val['skill_number']]['modified'] < $C_RES[$key]['modified']){
			$FULL_DATA[$val['skill_number']]['modified'] = $C_RES[$key]['modified'];
		}//

		unset($C_RES[$key]['modified_rego']);

		$C_RES[$key]['skill_name'] = $skill_names[$val['skill_number']];
		$C_RES[$key]['skill_category'] = $skill_categories[$val['skill_number']];

		$FULL_DATA[$val['skill_number']]['people']['competitors'][] = $C_RES[$key];
	}//foreach competitors

	//$FULL_DATA[$SKILL_NUMBER]['people']['competitors'] = $C_RES;
		
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
		if($FULL_DATA[$val['skill_number']]['modified'] < $E_RES[$key]['modified']){
			$FULL_DATA[$val['skill_number']]['modified'] = $E_RES[$key]['modified'];
		}//

		unset($E_RES[$key]['modified_rego']);		

		$E_RES[$key]['skill_name'] = $skill_names[$val['skill_number']];
		$E_RES[$key]['skill_category'] = $skill_categories[$val['skill_number']];

		$FULL_DATA[$val['skill_number']]['people']['experts'][] = $E_RES[$key];
	}//foreach competitors	

	//$FULL_DATA[$SKILL_NUMBER]['people']['experts'] = $E_RES;

	//WSS, WSSA, JP
	$OTHER_RES = $db->run($OTHER_SQL);

	//fix types for WSS AND WSSA and multiple skills
	foreach($OTHER_RES as $key=>$val){
		if($val['type'] == 'SM') $OTHER_RES[$key]['type'] = 'WSS';
		if($val['type'] == 'SMA') $OTHER_RES[$key]['type'] = 'WSSA';

		$multiskill = false;

		if(strstr($val['raw_skill'], ";")){
			$skills = explode(";", $val['raw_skill']);

			foreach($skills as $skill){
				$sql_temp = "SELECT ct.competition_trade_id, it.name_en FROM internal_trades it LEFT JOIN competition_trades ct ON ct.internal_trade_id = it.id WHERE ct.competition_id = {$COMPETITION_ID} AND it.id = {$skill};";
				$res_temp = $db->run($sql_temp);

				$OTHER_RES[$key]['skill_name'] = $skill_names[$res_temp[0]['competition_trade_id']];
				$OTHER_RES[$key]['skill_category'] = $skill_categories[$res_temp[0]['competition_trade_id']];
					
				$OTHER_RES[$key]['multiple_skills'][] = array('skill_number' => $res_temp[0]['competition_trade_id'], 'skill_name' => $res_temp[0]['name_en']);
				$FULL_DATA[$res_temp[0]['competition_trade_id']]['people']['others'][] = $OTHER_RES[$key];

				$multiskill = true;
			}
		}//multiple skills

		unset($OTHER_RES[$key]['raw_skill']);

		//check and update modified
		if($FULL_DATA[$val['skill_number']]['modified'] < $OTHER_RES[$key]['modified']){
			$FULL_DATA[$val['skill_number']]['modified'] = $OTHER_RES[$key]['modified'];
		}//		

		if(!$multiskill){
			$OTHER_RES[$key]['skill_name'] = $skill_names[$val['skill_number']];
			$OTHER_RES[$key]['skill_category'] = $skill_categories[$val['skill_number']];

			$FULL_DATA[$val['skill_number']]['people']['others'][] = $OTHER_RES[$key];
		}

	}//other
	//$FULL_DATA[$SKILL_NUMBER]['people']['others'] = $OTHER_RES;

	// $types = array('competitors', 'experts', 'others');
	// foreach($types as $type){
	// 	foreach($FULL_DATA[$SKILL_NUMBER]['people'][$type] as $key=>$val){
			
	// 		//localization for skill names and categories
	// 		//$FULL_DATA[$SKILL_NUMBER]['people'][$type][$key]['skill_name'] = $skill_names[$val['skill_number']];
	// 		//$FULL_DATA[$SKILL_NUMBER]['people'][$type][$key]['skill_category'] = $skill_categories[$val['skill_number']];

	// 		//encode images to base64
	// 		//$FULL_DATA['profiles'][$type][$key]['image_base64_thumb'] = base64_encode(file_get_contents($val['picture_url_thumb']));
	//  		//$FULL_DATA['profiles'][$type][$key]['image_base64'] = base64_encode(file_get_contents($val['picture_url']));

	// 	}//foreach
	// }//types
	 

	$cwd = getcwd();
	$gz = gzopen($cwd . "/cache/data-skills-all.json.gz", "w9");
	gzwrite($gz, json_encode($FULL_DATA));
	gzclose($gz);

?>
<p>Gzip created, <a href='aggregator/cache/data-skills-all.json.gz'>download</a></p>

<p>&raquo; <a href='index.php?edit=skill_descriptions'>Edit Skill Descriptions</a>
	<br />&raquo; <a href='index.php?edit=test_projects'>Edit Test Projects</a>
	<br />&raquo; <a href='index.php?edit=skill_highlights'>Edit Highlight Pictures</a></p>

<h1>Profile data review</h1>
<?php if (isset($_REQUEST['v'])): ?>
	
	<?php 
		$types = array('competitors', 'experts', 'others');
		foreach ($FULL_DATA as $SKILL_NUMBER => $value){
	?>
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
		<?php }//foreach ?>
		<?php endif ?>
		
