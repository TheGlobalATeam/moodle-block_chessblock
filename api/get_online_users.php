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

$table = 'user';

$sql = '
    SELECT * FROM mdl_user
    WHERE id != 1
    ORDER BY id ASC
';

$result = $DB->get_recordset_sql($sql, array());

$userList = array();

foreach ($result as $currentUser) {

    $userList[] = $arrayName = array(
        'id' => $currentUser->id,
        'username' => $currentUser->username,
        'lastaccess' => $currentUser->lastaccess,
    );

}

$result->close(); // Don't forget to close the recordset!

$returnobject = array();
$returnobject['status'] = true;
$returnobject['userList'] = $userList;


$jsondata = $returnobject;
header("Content-Type: application/json; charset=UTF-8");
echo json_encode($jsondata, JSON_PRETTY_PRINT);
