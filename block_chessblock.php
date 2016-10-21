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
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/pagelib.php');

class block_chessblock extends block_base {

    private $jsloaded = false;

    public function init() {
        GLOBAL $PAGE;

        $this->title = get_string('chessblock', 'block_chessblock');

    }

    public function applicable_formats() {
        return array('all' => true);
    }

    public function get_content() {

        global $CFG, $OUTPUT, $USER, $DB, $PAGE, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         = new stdClass;
        $this->content->text = "";
        $this->content->text .= '<h2>' . get_string('blocktitle', 'block_chessblock') . '</h2>';
        $this->content->text .= '<button id="newChessGame">' . get_string('newgamebutton', 'block_chessblock') . '</button><br>';
        $this->content->text .= '<div id="board" style="width: 100%"></div>';
        $this->content->text .= '<p>' . get_string('gamestatus', 'block_chessblock') . ':<span id="status"></span></p>';
        $this->content->text .= '<p id="download_fen_parent"></p>';
        $this->content->text .= '<p id="download_pgn_parent"></p>';
        $this->content->text .= '<script
        src="https://code.jquery.com/jquery-1.12.4.js"   integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="
        crossorigin="anonymous"></script>';

        if (!$this->jsloaded) {
            $this->jsloaded = true;
            $PAGE->requires->css('/blocks/chessblock/chessboardjs/css/chessboard-0.3.0.css?'.rand());
            $PAGE->requires->js('/blocks/chessblock/chessboardjs/js/chessboard-0.3.0.js?'.rand());
            $PAGE->requires->js('/blocks/chessblock/chessboardjs/js/chess.js?'.rand());
            $PAGE->requires->js('/blocks/chessblock/main.js?'.rand());

            $stringmanager = get_string_manager();
            $strings = $stringmanager->load_component_strings('block_chessblock', current_language());
            $PAGE->requires->js_init_call('loadLanguage', array($strings));
        }

        $this->content->footer = get_string('userid', 'block_chessblock') . ': ' . $USER->id;

        return $this->content;

    }
}
