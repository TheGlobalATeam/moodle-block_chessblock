'use strict'

console.log("chess!");

$( document ).ready(function() {
	console.log( "ready!" );

	//settings
	var config = {
		position: 'start',
		draggable: true,
		dropOffBoard: 'trash',
	};

	//init board using settings
	var board = ChessBoard('board', config);

});
