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
 * The main blockplugin file
 *
 * This file contains the initalizing of the chessblock plugin,
 * mainly defining the html of the view.
 *
 * @package block_chessblock
 * @copyright 2016 Global A-Team
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * MOODLE_INTERNAL - object, moodles intenral object.
  */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/pagelib.php');

/**
 * This is the main block class, extending block_base.
 *
 * The main class for chessblock plugin containing
 * the creating of the view and setting uup js files.
 *
 * @package block_chessblock
 * @copyright 2016 Global A-Team
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_chessblock extends block_base {

    /** @var boolean This variable checks if js is loaded */
    private $jsloaded = false;

    /**
     * Initalizing the block plugin.
     */
    public function init() {
        GLOBAL $PAGE;

        $this->title = get_string('chessblock', 'block_chessblock');

    }

    /**
     * The applicable_formats function
     */
    public function applicable_formats() {
        return array('all' => true);
    }


    /**
     * Returns the content og the block.
     *
     * @return stdClass containing html for the plugin view.
     */
    public function get_content() {

        global $CFG, $OUTPUT, $USER, $DB, $PAGE, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content         = new stdClass;
        $this->content->text = "";
        $this->content->text .= '<h2>' . get_string('blocktitle', 'block_chessblock') . '</h2>';
        $this->content->text .= '<button id="newChessGame">' . get_string('newgamebutton', 'block_chessblock') . '</button><br>';
        $this->content->text .= '<button id="loadPrevChessGame">' .
                                get_string('loadgamebutton', 'block_chessblock') .
                                '</button><br>';
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
