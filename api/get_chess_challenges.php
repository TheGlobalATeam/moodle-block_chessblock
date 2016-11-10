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

$table = 'block_chessblock_challenges';

$lastChallengeTime = -1;
if (isset($_GET['lastChallengeTime'])) {
    if(is_numeric($_GET['lastChallengeTime'])){
		$lastChallengeTime = $_GET['lastChallengeTime'];
	}
}

//last challenge first.
//TODO change to sql prepare, or switch to $DB->get_recordset using order
$sql = '
    SELECT * FROM mdl_block_chessblock_challenges
    WHERE challenged_user_id = "'.$USER->id.'"
	AND challenged_time > '.$lastChallengeTime.'
    ORDER BY challenged_time DESC
';

$result = $DB->get_recordset_sql($sql, array());
// 
// $result = $DB->get_recordset($table, array(
//     'challenged_user_id' => $USER->id,
// ));

$challengesList = array();

foreach ($result as $currentChallenge) {

    $challengesList[] = $arrayName = array(
        'id' => $currentChallenge->id,
        'challenger_user_id' => $currentChallenge->challenger_user_id,
        'challenged_user_id' => $currentChallenge->challenged_user_id,
		'challenged_time' => $currentChallenge->challenged_time,
		'lastChallengeTime' => $lastChallengeTime,
    );

}

$result->close(); // Don't forget to close the recordset!
$returnobject = array();

if (count($challengesList) == 0) {
    // Nothing found!
    $returnobject['status'] = false;
} else {
    // Returns last object (like ORDERY BY id DESC).
    $returnobject['status'] = true;
	$returnobject['challenges'] = $challengesList;

}

header("Content-Type: application/json; charset=UTF-8");
$jsondata = $returnobject;
echo json_encode($jsondata, JSON_PRETTY_PRINT);
