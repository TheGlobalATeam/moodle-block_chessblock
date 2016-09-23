'use strict'

console.log("chess!");


$( document ).ready(function() {

	var board;
	var game = new Chess();
	var statusEl = $('#status');
	var fenEl = $('#fen');
	var pgnEl = $('#pgn');

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

	var cfg = {
	  draggable: true,
	  position: 'start',
	  onDragStart: onDragStart,
	  onDrop: onDrop,
	  onSnapEnd: onSnapEnd,
	  pieceTheme: M.cfg.wwwroot + '/blocks/chessblock/chessboardjs/img/chesspieces/wikipedia/{piece}.png',
	};

	updateStatus();

	$('#newChessGame').click(function(){
	    console.log("starting!");

		game = new Chess();
		board = ChessBoard('board', cfg);

		updateStatus();
	});
});
