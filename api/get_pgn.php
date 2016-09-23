<?php
define('AJAX_SCRIPT', true);
require('../../config.php');
global $DB;
header('Content-Type: text/html; charset=utf-8');

$table = 'block_chessblock_games';
$result = $DB->get_records($table, array('white_user_id' => '10'));

echo html_writer::tag('h1', var_dump($result));
