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
 * mainly defining the Html of the view.
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

    //load the html file and use the Mustache_Engine as a template engine
    $mustache = new Mustache_Engine;
    $htmlFile = file_get_contents('index.html', true);
    $mustacheParserData = array(
      'wat' => "lool",
      'trans_blocktitle' => get_string('blocktitle', 'block_chessblock'),
      'trans_newgamebutton' => get_string('newgamebutton', 'block_chessblock'),
      'trans_loadgamebutton' => get_string('loadgamebutton', 'block_chessblock'),
      'trans_openMultiplayerButton' => get_string('openMultiplayerButton', 'block_chessblock'),
      'trans_gamestatus' => get_string('gamestatus', 'block_chessblock'),
      'loading_gif' => $CFG->wwwroot . '/blocks/chessblock/loading.gif',
    );
    //do the actuall load!
    $this->content->text .= $mustache->render($htmlFile, $mustacheParserData);

        if (!$this->jsloaded) {
            $this->jsloaded = true;
            $PAGE->requires->css('/blocks/chessblock/chessboardjs/css/chessboard-0.3.0.css?'.rand());
            $PAGE->requires->css('/blocks/chessblock/main.css?'.rand());
            $PAGE->requires->js('/blocks/chessblock/chessboardjs/js/chessboard-0.3.0.js?'.rand());
            $PAGE->requires->js('/blocks/chessblock/chessboardjs/js/chess.js?'.rand());
            $PAGE->requires->js('/blocks/chessblock/main.js?'.rand());
      $PAGE->requires->js('/blocks/chessblock/multiplayerNotificatin.js?'.rand());


            $stringmanager = get_string_manager();
            $strings = $stringmanager->load_component_strings('block_chessblock', current_language());
            $PAGE->requires->js_init_call('loadLanguage', array($strings));
        }

        $this->content->footer = get_string('userid', 'block_chessblock') . ': ' . $USER->id;

        return $this->content;
    }



}
