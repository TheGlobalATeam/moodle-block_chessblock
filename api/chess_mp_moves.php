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


//GET OR SET THE FEN FOR THE CURRENT MP GAME!
 /**
  * AJAX_SCRIPT - boolean, true.
  */
define('AJAX_SCRIPT', true);
require('../../../config.php');

require_login();

$returnobject = array();
$returnobject['status'] = false; //if prosessed OK

//set if player 1 or 2, (1 is default)
$isPlayer1 = true;
if(isset($_GET['isPlayer1'])){
    if(is_numeric($_GET['isPlayer1'])){
        if($_GET['isPlayer1'] == 0){
            $isPlayer1 = false;
        }
    }
}

//set other players ID
$otherPlayerID = -1;
if(isset($_GET['otherPlayerID'])){
    if(is_numeric($_GET['otherPlayerID'])){
        $otherPlayerID = $_GET['otherPlayerID'];

    }
}

//player 1 is white, and 2 is black
$whiteID = -1;
$blackID = -1;
if($isPlayer1){
    $whiteID = $USER->id;
    $blackID = $otherPlayerID;
}else{
    $whiteID = $otherPlayerID;
    $blackID = $USER->id;
}

//die($_POST['newFen'] ."|". $otherPlayerID);

//need to sdend along if im player one and the other players ID
//also fen for post

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newFen']) && $otherPlayerID != -1) {
    //where a fen is saved



    //omg no security:
    $newFen = $_POST['newFen'];
    $sql = '

        UPDATE mdl_block_chessblock_games
        SET game_fen = ?

        WHERE white_user_id = ?
        AND black_user_id = ?

    ';

    $DB->execute($sql, array($newFen, $whiteID, $blackID));
    $returnobject['status'] = true;

} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && $otherPlayerID != -1) {
    //get status of challenge (player 1 sends the request(challenger_user_id))

    $table = 'block_chessblock_games';

    $result = $DB->get_records($table, array(
        'white_user_id' => $whiteID,
        'black_user_id' => $blackID
    ));

    $returnobject = array();

    if (count($result) == 0) {
        // Nothing found!
        $returnobject['status'] = false;
    } else {
        // Returns last object (like ORDERY BY id DESC).
        $returnobject['status'] = true;

        $ids = array_keys($result);
        rsort($ids);
        //last element (like order by ID desc limit 1)
        $returnobject['gameData'] = $result[$ids[0]];
    }


}


header("Content-Type: application/json; charset=UTF-8");
$jsondata = $returnobject;
echo json_encode($jsondata, JSON_PRETTY_PRINT);
