<?php
/*
Some help:
https://github.com/trampgeek/moodle-qtype_coderunner/blob/master/ajax.php

*/
define('AJAX_SCRIPT', true);
require('../../../config.php');

require_login();
$userID = $USER->id;
//$userID = 2;

$returnObject = array();

if(isset($_POST['gameFEN']) && isset($_POST['gamePGN'])){

	$gameFEN = $_POST['gameFEN'];
	$gamePGN = $_POST['gamePGN'];

	$table = 'block_chessblock_games';
	$record = new stdClass();
	$record->game_fen  = $gameFEN;
	$record->game_pgn = $gamePGN;
	$record->white_user_id = $userID;
	$record->black_user_id = '-1';
	$status = $DB->insert_record($table, $record, false);

	$returnObject['status'] = true;

}else{

	$returnObject['status'] = false;

}

header("Content-Type: application/json; charset=UTF-8");
//header('Access-Control-Allow-Origin: *');
$jsonData = $returnObject;
echo json_encode($jsonData, JSON_PRETTY_PRINT);
