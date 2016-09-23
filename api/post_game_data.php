<?php
define('AJAX_SCRIPT', true);
require('../../config.php');
global $DB;
header('Content-Type: text/html; charset=utf-8');

$table = 'block_chessblock_games';
$record = new stdClass();
$record->game_fen  = 'FEN';
$record->game_pgn = 'PGN';
$record->white_user_id = '10';
$record->black_user_id = '11';
$status = $DB->insert_record($table, $record, false);
echo html_writer::tag('div', 'Success!');
