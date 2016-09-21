<?php
//WORK IN SOMEWHAT PROGRESS for API endpoint. Just a copy from a repo which uses AJAX inside a block plugin
// https://github.com/dmiletic/moodle/blob/sample/laterdude/blocks/laterdude/data.php
define('AJAX_SCRIPT', true);
require('../../config.php');
try {
    require_login();
    require_sesskey();
    header('Content-Type: text/html; charset=utf-8');
    echo html_writer::tag('h1', 'We are the champions!');
} catch (Exception $e) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
    if (isloggedin()) {
        header('Content-Type: text/plain; charset=utf-8');
        echo $e->getMessage();
    }
}
