<?php

/*
Some help:
https://github.com/trampgeek/moodle-qtype_coderunner/blob/master/ajax.php

*/
define('AJAX_SCRIPT', true);
require('../../../config.php');

require_login();

$table = 'block_chessblock_games';
//$USER->id contains the ID if the user
$result = $DB->get_records($table, array(
	'white_user_id' => $USER->id,
	'black_user_id' => -1
));

$returnObject = array();

if(count($result) == 0){
	//nothing found!
	$returnObject['status'] = false;
}else{
	//returns last object (like ORDERY BY id DESC)
	$returnObject['status'] = true;

	$ids = array_keys($result);
	arsort($ids);
	$returnObject['gameData'] = $result[$ids[0]];
}


header("Content-Type: application/json; charset=UTF-8");
//header('Access-Control-Allow-Origin: *');
$jsonData = $returnObject;
echo json_encode($jsonData, JSON_PRETTY_PRINT);
