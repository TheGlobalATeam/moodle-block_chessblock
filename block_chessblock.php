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

		global $CFG, $OUTPUT, $USER, $DB, $PAGE, $USER;

		if ($this->content !== null) {
			return $this->content;
		}

		$this->content         =  new stdClass;
		$this->content->text = "";

		//first element with no .=, just =
		$this->content->text .= "<h2>Chess!</h2>";
		$this->content->text .= '<button id="newChessGame">New Game of AI vs me chess</button><br>';
		$this->content->text .= '<div id="board" style="width: 300px"></div>';
		$this->content->text .= '<p>Status: <span id="status"></span></p>';
		$this->content->text .= '<p>FEN: <span id="fen"></span></p>';
		$this->content->text .= '<p>PGN: <span id="pgn"></span></p>';
		$this->content->text .= '<script   src="https://code.jquery.com/jquery-1.12.4.js"   integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="   crossorigin="anonymous"></script>';

		//loading js file, while preventing moodle catching. probably a better way somewhere...
		if(!$this->jsLoaded){
			$this->jsLoaded = true;
			$PAGE->requires->css('/blocks/chessblock/chessboardjs/css/chessboard-0.3.0.css?'.rand());
			$PAGE->requires->js('/blocks/chessblock/chessboardjs/js/chessboard-0.3.0.js?'.rand());
			$PAGE->requires->js('/blocks/chessblock/chessboardjs/js/chess.js?'.rand());
			$PAGE->requires->js('/blocks/chessblock/main.js?'.rand());
		}

		$insIRD = $this->insertTestRecord();
		//$fen = $this->retriveTestRecordFen(1);

		//$this->content->footer = 'Last Insert status: '.$insIRD . ' | Fen of id 3: '.$fen3;
		$this->content->footer = "User ID: ".$USER->id;

		return $this->content;

	}

	//https://docs.moodle.org/dev/Data_manipulation_API
	//https://docs.moodle.org/dev/Data_manipulation_API#Inserting_Records

	//insert new row
    private function insertTestRecord() {

        global $CFG, $OUTPUT, $USER, $DB, $PAGE;

        $table = 'block_chessblock_games';

        $record = new stdClass();
        $record->game_fen  = 'FEN2';
        $record->game_pgn = 'PGN2';
        $record->white_user_id = $USER->id;
        $record->black_user_id = '-1';
        $status = $DB->insert_record($table, $record, false);
        return $status;
		
    }

    // Pulling all records that fit parms
    private function retriveTestRecordFen($index) {

        global $CFG, $OUTPUT, $USER, $DB, $PAGE;

        $table = 'block_chessblock_games';
        $result = $DB->get_records($table, array('white_user_id' => '10', 'id' => $index));

        return $result[$index]->game_fen;
    }
}
