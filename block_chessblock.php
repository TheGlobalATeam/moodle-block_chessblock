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
		$fen3 = $this->retriveTestRecordFen(3);

		$this->content->footer = 'Last Insert status: '.$insIRD . ' | Fen of id 3: '.$fen3;



		return $this->content;

	}

	//https://docs.moodle.org/dev/Data_manipulation_API
	//https://docs.moodle.org/dev/Data_manipulation_API#Inserting_Records

	//insert new row
	private function insertTestRecord(){

		global $CFG, $OUTPUT, $USER, $DB, $PAGE;

		$table = 'block_chessblock_positions';

		$record = new stdClass();
		$record->game_fen  = 'YOLO';
		$record->game_pgn = 'SWAG';
		$record->user_id = '1337';
		$record->player_color = '0';
		$status = $DB->insert_record($table, $record, false);
		return $status;

	}

	//pulling all records that fit parms
	private function retriveTestRecordFen($index){

		global $CFG, $OUTPUT, $USER, $DB, $PAGE;

		$table = 'block_chessblock_positions';
		$result = $DB->get_records($table, array('user_id'=>'1337', 'id' => $index));

	//	var_dump($result[$index]);


		return $result[$index]->game_fen;

	}
}
