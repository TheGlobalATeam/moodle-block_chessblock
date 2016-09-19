<?php

require_once($CFG->libdir . '/pagelib.php');

class block_chessblock extends block_base {

	private $jsLoaded = false;

    public function init() {
		GLOBAL $PAGE;

        $this->title = get_string('chessblock', 'block_chessblock');




    }

	public function applicable_formats() {
        return array('all' => true);
    }

	public function get_content() {

        global $CFG, $OUTPUT, $USER, $DB, $PAGE;

		//loading js file, while preventing moodle catching. probably a better way somewhere...
		if(!$this->jsLoaded){
			$this->jsLoaded = true;
			$PAGE->requires->js('/blocks/chessblock/main.js?'.rand());
		}

        if ($this->content !== null) {
          return $this->content;
        }

        $this->content         =  new stdClass;
		$this->content->text = "";

        //first element with no .=, just =
        $this->content->text .= "<h2>Chess!</h2>";




        $this->content->footer = 'The end of my chess block';

        return $this->content;

    }
}
