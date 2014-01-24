<?php error_reporting(E_ALL); require_once("../aggregator/setup.inc.php"); ?>

<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    header("Content-Type: application/json");


//user => password
//$users = array('app_feedback' => 'GC4YVWOJ3fLBI1gS');
$auth_user = 'app_feedback';
$auth_pass = 'GC4YVWOJ3fLBI1gS';


// analyze the PHP_AUTH_DIGEST variable
if (empty($_POST) || !isset($_POST['auth_user']) || !isset($auth_pass) || $_POST['auth_user'] != $auth_user || $_POST['auth_pass'] != $auth_pass){
    echo json_encode(array('status' => '202', 'message' => 'Error: Authentication failed'));
    exit();
    //die('Wrong Credentials!');
}

// ok, valid username & password

    if(!isset($_POST['from']) || !isset($_POST['from_email']) || !isset($_POST['subject']) || !isset($_POST['message'])){
        echo json_encode(array('status' => '101', 'message' => 'Error: missing POST data variables'));
        exit();
    }//test post data

    $db = getConnection();

    //data
    $from = mysql_escape_string($_POST['from']);
    $from_email = mysql_escape_string($_POST['from_email']);
    $subject = mysql_escape_string($_POST['subject']);
    $message = mysql_escape_string($_POST['message']);

    $sql = "INSERT INTO app_feedback (`from`, from_email, subject, message, created, modified) VALUES('{$from}', '{$from_email}', '{$subject}', '{$message}', NOW(), NOW());";

    $db->run($sql);
    echo json_encode(array('status' => '0', 'message' => 'Feedback successful'));

?>
