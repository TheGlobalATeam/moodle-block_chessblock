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
 * Post game data
 *
 * Returns boolean if post had successed.
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
$userid = $USER->id;

$returnobject = array();

if (isset($_POST['gameFEN']) && isset($_POST['gamePGN'])) {

    $gamefen = $_POST['gameFEN'];
    $gamepgn = $_POST['gamePGN'];

    $table = 'block_chessblock_games';
    $record = new stdClass();
    $record->game_fen  = $gamefen;
    $record->game_pgn = $gamepgn;
    $record->white_user_id = $userid;
    $record->black_user_id = '-1';
    $status = $DB->insert_record($table, $record, false);

    $returnobject['status'] = true;

} else {

    $returnobject['status'] = false;

}

header("Content-Type: application/json; charset=UTF-8");

$jsondata = $returnobject;
echo json_encode($jsondata, JSON_PRETTY_PRINT);
