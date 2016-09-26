//'use strict'

console.log("chess!");


$( document ).ready(function() {

	var board;
	var game = new Chess();
	var statusEl = $('#status');
	var fenEl = $('#fen');
	var pgnEl = $('#pgn');
	var gameRunning = false;

	// do not pick up pieces if the game is over
	// only pick up pieces for the side to move
	var onDragStart = function(source, piece, position, orientation) {
		// if (game.game_over() === true ||
		// 	(game.turn() === 'w' && piece.search(/^b/) !== -1) ||
		// 	(game.turn() === 'b' && piece.search(/^w/) !== -1)) {
		// 		return false;
		// }

		//only white may move, on their turn
		if (game.in_checkmate() === true ||
			game.in_draw() === true ||
			piece.search(/^b/) !== -1) {
			return false;
		}
	};

	var onDrop = function(source, target) {
		// see if the move is legal
		var move = game.move({
			from: source,
			to: target,
			promotion: 'q' // NOTE: always promote to a queen for example simplicity
		});

		// illegal move
		if (move === null) return 'snapback';

		updateStatus();
		window.setTimeout(makeRandomMove, 250);
	};

	// update the board position after the piece snap
	// for castling, en passant, pawn promotion
	var onSnapEnd = function() {
		board.position(game.fen());
	};

	var makeRandomMove = function() {
		var possibleMoves = game.moves();

		// game over
		if (possibleMoves.length === 0) return;

		var randomIndex = Math.floor(Math.random() * possibleMoves.length);
		game.move(possibleMoves[randomIndex]);
		board.position(game.fen());
		updateStatus();
	};

	var updateStatus = function() {
		var status = '';
		var moveColor = 'White';

		if (game.turn() === 'b') {
			moveColor = 'Black';
		} if (game.in_checkmate() === true) {
			// checkmate
			status = 'Game over, ' + moveColor + ' is in checkmate.';
		} else if (game.in_draw() === true) {
			// draw?
			status = 'Game over, drawn position';
		} else {
			// game still on
			status = moveColor + ' to move';
			// check?
			if (game.in_check() === true) {
				status += ', ' + moveColor + ' is in check';
			}
		}
		statusEl.html(status);
		fenEl.html(game.fen());
		pgnEl.html(game.pgn());
	};

	var saveGame = function(){

		if(!gameRunning){
			console.log("need to start a game in order to save");
			return;
		}

		//CAN't FIND any documentation...
		//POST to api
		Y.io(
			M.cfg.wwwroot + "/blocks/chessblock/api/post_game_data.php", {
				method: "POST",
				data: 'gameFEN='+game.fen()+'&gamePGN=NOPE',
				on: {
					success: function(io, o, arguments) {

						console.log("SAVED!");
						//console.log(o.response);

					}
				}
			}
		);

	}

	var cfg = {
	  draggable: true,
	  position: 'start',
	  onDragStart: onDragStart,
	  onDrop: onDrop,
	  onSnapEnd: onSnapEnd,
	  pieceTheme: M.cfg.wwwroot + '/blocks/chessblock/chessboardjs/img/chesspieces/wikipedia/{piece}.png',
	};

	updateStatus();



	$('#saveChessGame').click(function(){

		saveGame();

	});

	$('#newChessGame').click(function(){

		game = new Chess();
		//start fen
		cfg.position = "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1";
		board = new ChessBoard('board', cfg);
		gameRunning = true;

		updateStatus();
	});

	$('#loadPrevChessGame').click(function(){

		//Loading from API
		Y.io(
			M.cfg.wwwroot + "/blocks/chessblock/api/get_game_data.php", {
				method: "GET",
				on: {
					success: function(io, o, arguments) {

						var gameData = JSON.parse(o.response);
						if( gameData.gameData.game_fen != null &&  gameData.gameData.game_fen.length > 0){
							var cfgLoad = cfg;
							cfgLoad.position = gameData.gameData.game_fen;

							game = new Chess(gameData.gameData.game_fen);
							board = new ChessBoard('board', cfgLoad);
							gameRunning = true;

						}

					}
				}
			}
		);

	});

});
