<?php error_reporting(E_ALL); require_once("../aggregator/setup.inc.php"); ?>

<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    header("Content-Type: application/json");



$db = getConnection();



//user => password
//$users = array('app_upload' => 'CbRdThE0Mv5y6taK');
$auth_user = 'app_upload';
$auth_pass = 'CbRdThE0Mv5y6taK';



if (empty($_POST) || !isset($_POST['auth_user']) || !isset($auth_pass) || $_POST['auth_user'] != $auth_user || $_POST['auth_pass'] != $auth_pass){
    echo json_encode(array('status' => '202', 'message' => 'Error: Authentication failed'));
    exit();
    //die('Wrong Credentials!');
}



// ok, valid username & password

    $file = date('Ymd_His') . "_" . rand(10000, 99999) . ".png";

    if(!isset($_POST['caption']) || !isset($_POST['skill']) || !isset($_POST['author']) || !isset($_POST['description'])){
        echo json_encode(array('status' => '101', 'message' => 'Error: missing POST data variables'));
        exit();
    }//test post data

    //data
    $caption = mysql_escape_string($_POST['caption']);
    $skill = mysql_escape_string($_POST['skill']);
    $author = mysql_escape_string($_POST['author']);
    $description = mysql_escape_string($_POST['description']);

    $sql = "INSERT INTO app_photo_uploads (caption, skill, author, description, fileurl, filename, created, modified) VALUES('{$caption}', '{$skill}', '{$author}', '{$description}', '".SITE_URL."photo_submissions/photos/{$file}', '{$file}', NOW(), NOW());";
    $db->run($sql);
?>
<?php
$uploaddir = 'photos/';//your-path-to-upload

$response = new stdClass();
try {
    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && 
            empty($_POST) &&
            empty($_FILES) && 
            $_SERVER['CONTENT_LENGTH'] > 0) {

            $displayMaxSize = ini_get('post_max_size');


            switch (substr($displayMaxSize, -1)) {
                case 'G':
                    $displayMaxSize = $displayMaxSize * 1024;
                case 'M':
                    $displayMaxSize = $displayMaxSize * 1024;
                case 'K':
                    $displayMaxSize = $displayMaxSize * 1024;
            }

            $errMessage = 'Your file is too large. ' . 
                    $_SERVER[CONTENT_LENGTH] . 
                    ' bytes exceeds the maximum size of ' . 
                    $displayMaxSize . ' bytes.';            
        } else {
            switch ($_FILES['photo']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $errMessage = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $errMessage = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errMessage = "The uploaded file was only partially uploaded";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errMessage = "No file was uploaded";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errMessage = "Missing a temporary folder";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errMessage = "Failed to write file to disk";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $errMessage = "File upload stopped by extension";
                    break;
                default:
                    $errMessage = "Unknown upload error";
                    break;
            }
        }

        $response->success = false;
        $response->message = $errMessage;
        $status = ($_FILES['photo']['error'] != null) ? $_FILES['photo']['error'] : -2;
        $response->status = $status;
        $response->photo_url = '';

    } else {    
        //$uploadfile = $uploaddir . basename($_FILES['photo']['name']);
        $uploadfile = $uploaddir . $file;

        if (is_uploaded_file($_FILES['photo']['tmp_name']) && 
            move_uploaded_file($_FILES['photo']['tmp_name'], $uploadfile)) {

            $response->success = true;
            $response->status = 0;
            $response->photo_url = SITE_URL."photo_submissions/photos/{$file}";
            $response->message = false;
        } else {
            $response->success = false;
            var_dump($uploadfile);
            $response->message = 'File was uploaded but not saved on server';
            $response->status = -1;
            $response->photo_url = '';
        }    
    }
} catch (Exception $e) {
    $response->success = false;
    $response->message = $e->getMessage();
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
exit;
?>