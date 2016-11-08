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

//default

if (isset($_POST['challengedUserID'])) {

    if(is_numeric($_POST['challengedUserID'])){

        //TODO check if valid userID

        $challengedUserID = $_POST['challengedUserID'];

        //TDO from here

        $table = 'block_chessblock_challenges';
        $record = new stdClass();
        $record->challenger_user_id  = $USER->id;
        $record->challenged_user_id = $challengedUserID;
        $record->challenged_time = time();
        $status = $DB->insert_record($table, $record, false);

        $returnobject['status'] = true;

    }

}

header("Content-Type: application/json; charset=UTF-8");

$jsondata = $returnobject;
echo json_encode($jsondata, JSON_PRETTY_PRINT);
