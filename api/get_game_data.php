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

define('AJAX_SCRIPT', true);
require('../../../config.php');

require_login();

$table = 'block_chessblock_games';

$result = $DB->get_records($table, array(
    'white_user_id' => $USER->id,
    'black_user_id' => -1
));

header('Content-Type: text/html; charset=utf-8');

$returnobject = array();

if (count($result) == 0) {
    // Nothing found!
    $returnobject['status'] = false;
} else {
    // Returns last object (like ORDERY BY id DESC).
    $returnobject['status'] = true;

    $ids = array_keys($result);
    rsort($ids);
    $returnobject['gameData'] = $result[$ids[0]];
}

$jsondata = $returnobject;
echo json_encode($jsondata, JSON_PRETTY_PRINT);
