<?php
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
// tolgo il tempo limite di esecuzione
set_time_limit(0);

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
require_once '../lib/db/Db.php';
require_once '../lib/config.php';
$result = array();

session_start();
$platform_id = isset($_SESSION['jigowatt']['user_id']) ? $_SESSION['jigowatt']['user_id'] : "-1";

// Set your connection info here
Db::setConnectionInfo(DB_NAME, DB_USER, DB_PASSWORD);

// Inserting a tag
$data = isset($_REQUEST["data"]) ? $_REQUEST["data"] : ""; 
$token = isset($_REQUEST["access_token"]) ? $_REQUEST["access_token"] : ""; 

if ($data !="" && $token !=""){
    $record = array("INSTAGRAM_ID" => $data['id'], "PLATFORM_ID" => $platform_id, "USERNAME" => addslashes($data['username']), "PROFILE_PICTURE" => $data['profile_picture'], "FULL_NAME" => addslashes($data['full_name']), "FOLLOWED_BY" => $data['counts']['followed_by'], "FOLLOWS" => $data['counts']['follows'], "MEDIA" => $data['counts']['media'], "ACCESS_TOKEN" => $token);
	$result = Db::getLastInsertId('INSERT INTO instagram (INSTAGRAM_ID, PLATFORM_ID, USERNAME, PROFILE_PICTURE, FULL_NAME, FOLLOWED_BY, FOLLOWS, MEDIA, ACCESS_TOKEN) VALUES(:INSTAGRAM_ID, :PLATFORM_ID, :USERNAME, :PROFILE_PICTURE, :FULL_NAME, :FOLLOWED_BY, :FOLLOWS, :MEDIA, :ACCESS_TOKEN) ON DUPLICATE KEY
	UPDATE PROFILE_PICTURE=:PROFILE_PICTURE, FULL_NAME=:FULL_NAME, FOLLOWED_BY=:FOLLOWED_BY, FOLLOWS=:FOLLOWS, MEDIA=:MEDIA, ACCESS_TOKEN=:ACCESS_TOKEN', $record);
}

echo json_encode($result);
?>
