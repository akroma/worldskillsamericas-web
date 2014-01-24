<?php 	

	function getConnection(){
		$db = new db("mysql:host=127.0.0.1;dbname=wiw2;port=3306", "root", "");
		//$db = new db("mysql:host=localhost;dbname=mydb", "dbuser", "dbpasswd");
		return $db;		
	}

function getJoomlaConnection(){
		$db = new db("mysql:host=127.0.0.1;dbname=Joomla_staging;port=3306", "root", "");
		//$db = new db("mysql:host=localhost;dbname=mydb", "dbuser", "dbpasswd");
		return $db;		
	}	
	
	function getSkillSponsors($skillnum){
		global $db;

		$sql = "SELECT * FROM skill_sponsors WHERE competition_id = " . COMPETITION_ID . " AND competition_trade_id = '{$skillnum}';";
		$res = $db->run($sql);

//		$sponsors = array();
		$sponsors = array(
			
		);

		if($res !== false){
			foreach($res as $key=>$val){
				$tmp_sponsors = array('id' => $val['id'],
					'name' => $val['name'],
					'competition_id' => $val['competition_id'],
					'skill' => $val['competition_trade_id'],
					'logo_thumb_url' => 'http://aggregator.worldskills.org/assets/xcrud/uploaded_images/' . $val['logo_thumb_url'],
					'profile_image_url' => 'http://aggregator.worldskills.org/assets/xcrud/uploaded_images/' . $val['profile_image_url'],
					'profile_title' => array('localized_content' => array(
						'EN' => $val['profile_title_en'],
						'DE' => $val['profile_title_de']
						)),
					'profile_description' => array('localized_content' => array(
						'EN' => $val['profile_description_en'],
						'De' => $val['profile_description_de']
						)),
					'profile_video_url' => $val['profile_video_url'],
					'read_more_url' => $val['read_more_url'],
					'created' => $val['created'],
					'modified' => $val['modified']
				);	

				$sponsors[] = array(
					'group' => 'Skill Sponsors',
					'items' => $tmp_sponsors
				);
				//$sponsors['items'][] = $tmp_sponsors;
				//$res[$key]['profile_title']['localized_content']['EN'] = $val['profile_title_en'];
				//$res[$key]['profile_title']['localized_content']['DE'] = $val['profile_title_de'];
	//
	//			//$res[$key]['profile_description']['localized_content']['EN'] = $val['profile_description_en'];
	//			//$res[$key]['profile_description']['localized_content']['DE'] = $val['profile_description_de'];
	//
	//			//unset($res[$key]['profile_title_en']);
	//			//unset($res[$key]['profile_title_de']);
	//			//unset($res[$key]['profile_description_en']);
				//unset($res[$key]['profile_description_de']);
				//unset($res[$key]['competition_id']);
				//unset($res[$key]['competition_trade_id']);
			}

			return $sponsors;

			//return $res;
		}
		
		return array();
	}

	function getTestProject($skillnum){
		global $db;

		$sql = "SELECT * FROM test_projects WHERE competition_id = " . COMPETITION_ID . " AND competition_trade_id = '{$skillnum}';";
		$res = $db->run($sql);

		// if($res === false || count($res) == 0){
		// 	$tmp_json = '{"id":"'.$skillnum.'","competition_id":"42","competition_trade_id":"'.$skillnum.'","images":["http:\/\/www.worldskillsportal.com\/images\/stories\/NLS\/17_web_design_01.jpg","http:\/\/www.worldskillsportal.com\/images\/stories\/NLS\/17_web_design_01.jpg"],"created":"2013-05-15 13:20:50","modified":"2013-05-15 13:20:50","title":{"localized_content":{"EN":"01 Project","DE":"Project DE"}},"subtitle":{"localized_content":{"EN":"Subtitle","DE":"Subtitle DE"}},"description":{"localized_content":{"EN":"Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam, commodi cupiditate possimus aliquid suscipit doloribus rerum nulla eius illum nam. Odio, alias itaque ullam nobis minus quod maiores perspiciatis qui?","DE":"Lorem Ipsum in DE"}}}'; 
		// 	$res = json_decode($tmp_json);
		// 	return $res;
		// } 

		$img = json_decode($res[0]['images']);

		$res[0]['id'] = $skillnum;
		$res[0]['images'] = $img;		
		$res[0]['title']['localized_content']['EN'] = $res[0]['title_en'];
		$res[0]['title']['localized_content']['DE'] = $res[0]['title_de'];

		$res[0]['subtitle']['localized_content']['EN'] = $res[0]['subtitle_en'];
		$res[0]['subtitle']['localized_content']['DE'] = $res[0]['subtitle_de'];

		$res[0]['description']['localized_content']['EN'] = clean_for_json($res[0]['description_en'], false);
		$res[0]['description']['localized_content']['DE'] = clean_for_json($res[0]['description_de'], false);

		unset($res[0]['title_en']);
		unset($res[0]['title_de']);

		unset($res[0]['subtitle_en']);
		unset($res[0]['subtitle_de']);

		unset($res[0]['description_en']);
		unset($res[0]['description_de']);
		
		//unset($res[0]['competition_id']);
		//unset($res[0]['competition_trade_id']);

		return $res[0];
	}

	function getEventSchedule($skillnum){
		global $db;

		$sql = "SELECT * FROM app_event_schedule WHERE skill = {$skillnum} ORDER BY datestamp, start_time;";
		$res = $db->run($sql);
		$retval = array();

		if(count($res) > 0){
			foreach($res as $r){
				unset($r['id']);
				$retval[] = (array)$r;
			}
		}

		return $retval;
	}	

	function getSkillImages($skillnum){
		global $db;

		$sql = "SELECT highlight_images FROM internal_trades WHERE id = (SELECT internal_trade_id FROM competition_trades WHERE competition_id = " . COMPETITION_ID . " AND competition_trade_id = '{$skillnum}');";
		$res = $db->run($sql);


		$img = json_decode($res[0]['highlight_images']);


		return $img;
		//return array('THESE IMAGES ARE NOT PROPER RESOLUTION OR ASPECT RATIO', 'http://www.worldskillsportal.com/images/stories/NLS/17_web_design_01.jpg', 'http://www.worldskillsportal.com/images/stories/NLS/17_web_design_02.jpg');
	}

	function getLastUpdated($file){
		global $db;

		$sql = "SELECT * FROM app_updates WHERE file = '{$file}';";
		$res = $db->run($sql);

		if($res === false || count($res) == 0 || !isset($res[0]['last_updated'])) return '0000-00-00 00:00:00';

		return $res[0]['last_updated'];
	}

	function writeGzip($file, $data, $timestamp){		
		global $db;

		$cwd = getcwd();
		$gz = gzopen($cwd . "/cache/{$file}", "w9");
		gzwrite($gz, json_encode($data));
		gzclose($gz);

		$fp = fopen($cwd . "/cache/" . substr($file, 0, -3), "w");
		fwrite($fp, json_encode($data));		
		fclose($fp);


		if(DEBUG){
			$error = false;
			switch (@json_last_error()) {
        	case JSON_ERROR_NONE:
        	    //echo ' - No errors';
        		$error = "NO ERRORS";
        	break;
        	case JSON_ERROR_DEPTH:
        	    $error = ' - Maximum stack depth exceeded';
        	break;
        	case JSON_ERROR_STATE_MISMATCH:
        	    $error = ' - Underflow or the modes mismatch';
        	break;
        	case JSON_ERROR_CTRL_CHAR:
        	    $error = ' - Unexpected control character found';
        	break;
        	case JSON_ERROR_SYNTAX:
        	    $error = ' - Syntax error, malformed JSON';
        	break;
        	case JSON_ERROR_UTF8:
        	    $error = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        	break;
        	default:
        	    $error = ' - Unknown error';
        	break;
    		}		
        	   if($error !== false) echo "<h3>JSON: {$error}</h3>";
        }//DEBUG only

		//check if row exists
		$sql = "SELECT * FROM app_updates WHERE file = '{$file}';";
		$res = $db->run($sql);

		if(count($res) > 0){
			$sql = "UPDATE app_updates SET last_updated = NOW(), modified = '{$timestamp}' WHERE file = '{$file}';";
			//$sql = "UPDATE app_updates SET last_updated = '{$timestamp}', modified = '{$timestamp}' WHERE file = '{$file}';";
		}
		else $sql = "INSERT INTO app_updates VALUES('{$file}', NOW(), NOW(), '{$timestamp}');";
		//else $sql = "INSERT INTO app_updates VALUES('{$file}', '{$timestamp}', NOW(), '{$timestamp}');";
		//create	

		$db->run($sql);
	}

	function clean_for_json($str, $nl2br = false){
		//$str = iconv('\'', '\\\'', $str);
		if($nl2br)
			return nl2br(iconv("cp1252", "UTF-8//TRANSLIT", $str));
		else
			return iconv("cp1252", "UTF-8//TRANSLIT", $str);
		//return $str;
	}


?>