<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * APi for getting game data.
 *
 * Returns json data of game states.
 *
 * @package block_chessblock
 * @copyright 2016 Global A-Team
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * AJAX_SCRIPT - boolean, true.
  */
define('AJAX_SCRIPT', true);
require('../../../config.php');

require_login();

$returnobject = array();
$returnobject['status'] = false; //if prosessed OK


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['challengerUserID']) && isset($_POST['challengeResponce'])) {
    //where player 2 sets their responce tot the challenge
    $challengerUserID = -1;
    if(is_numeric($_POST['challengerUserID'])){
        $challengerUserID = $_POST['challengerUserID'];
    }

    $challengeResponce = -1;
    if(is_numeric($_POST['challengeResponce'])){
        $challengeResponce = $_POST['challengeResponce'];
    }

    $sql = '

        UPDATE mdl_block_chessblock_challenges
        SET challenged_accepts = ?

        WHERE challenger_user_id = ?
        AND challenged_user_id = ?

    ';

    $DB->execute($sql, array($challengeResponce, $challengerUserID, $USER->id));

    //setup MP block_chessblock_games
    if($challengeResponce == 1){
        //default
        $gamefen = "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1";
        $gamepgn = "NOPE";

        $table = 'block_chessblock_games';
        $record = new stdClass();
        $record->game_fen  = $gamefen;
        $record->game_pgn = $gamepgn;
        $record->white_user_id = $challengerUserID;
        $record->black_user_id = $USER->id;
        $status = $DB->insert_record($table, $record, false);
    }

    $returnobject['status'] = true;


} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['challengedUserID'])) {
    //get status of challenge (player 1 sends the request(challenger_user_id))

    $challengedUserID = -1;
    if(is_numeric($_GET['challengedUserID'])){
        $challengedUserID = $_GET['challengedUserID'];
    }

    //gets only last
    $sql = '
        SELECT * FROM mdl_block_chessblock_challenges
        WHERE challenger_user_id = "'.$USER->id.'"
        AND challenged_user_id = "'.$challengedUserID.'"
        ORDER BY challenged_time DESC
        LIMIT 1
    ';

    $result = $DB->get_recordset_sql($sql, array());



    foreach ($result as $currentChallenge) {

        $returnobject['status'] = true;
        $returnobject['challengeAccepted'] = false;
        $returnobject['challengeDenied'] = false;

        //default is -1, which is no responce yet
        if($currentChallenge->challenged_accepts == 1){
            $returnobject['challengeAccepted'] = true;
        }else if ($currentChallenge->challenged_accepts == 0) {
            $returnobject['challengeDenied'] = true;
        }

    }

    $result->close(); // Don't forget to close the recordset!

	if($returnobject['challengeAccepted'] === true || $returnobject['challengeDenied'] === true){

		//deleting challenge affter pullig a denied or comfirmed challenge
		$table = 'block_chessblock_challenges';

		//delete all prev challenges
		$conditions = array(
			'challenger_user_id' => $USER->id,
			'challenged_user_id' => $challengedUserID
		);
		$DB->delete_records($table, $conditions);


	}




}


header("Content-Type: application/json; charset=UTF-8");
$jsondata = $returnobject;
echo json_encode($jsondata, JSON_PRETTY_PRINT);
