var language = {};

function loadLanguage(Y, currentLanguage) {
    language = currentLanguage;
}

$( document ).ready(function() {

    var board;
    var game = new Chess();
    var statusEl = $('#status');
    var gameRunning = false;

    // Do not pick up pieces if the game is over only pick up pieces for the side to move.
    var onDragStart = function(source, piece, position, orientation) {
        // Only white may move, on their turn.
        if (game.in_checkmate() === true ||
            game.in_draw() === true ||
            piece.search(/^b/) !== -1) {
                return false;
        }
    };

    var onDrop = function(source, target) {
        removeHighlight();

        // See if the move is legal.
        var move = game.move({
            from: source,
            to: target,
            promotion: 'q' // NOTE: always promote to a queen for example simplicity.
        });

        // Illegal move.
        if (move === null) return 'snapback';

        updateStatus();
        updateDownloadLinks();
        saveGame();
        window.setTimeout(makeRandomMove, 250);
    };

    // Update the board position after the piece snap for castling, en passant, pawn promotion.
    var onSnapEnd = function() {
        board.position(game.fen());
    };

    var removeHighlight = function(square) {
        $('#board .square-55d63').css('background', '');
    }

    var highlightSquare = function(square) {
        var squareElement = $('#board .square-' + square);
        var isSqaureBlack = (squareElement.hasClass('black-3c85d') === true);
        var backgroundColor = (isSqaureBlack)? '#696969': '#a9a9a9';
        squareElement.css('background', backgroundColor);
    }

    var onMouseoverSquare = function(square, piece) {

        // Get possible moves for this square.
        var moves = game.moves({
            square: square,
            verbose: true
        })

        if (moves.length === 0) return;

        // Highlight hovered square and leagl moves:
        highlightSquare(square);
        for (var i = 0; i < moves.length; ++i) {
            highlightSquare(moves[i].to);
        }
    }

    var onMouseoutSquare = function(square, piece) {
        removeHighlight();
    }

    var putDownloadLinks= function() {
        var fen_link = '<a id="download_fen">' + language['download'] + ' FEN</a>'
        var pgn_link = '<a id="download_pgn">' + language['download'] + ' PGN</a>'
        $('#download_fen_parent').html(fen_link);
        $('#download_pgn_parent').html(pgn_link);
        updateDownloadLinks();
    }

    var updateDownloadLinks = function() {
        var fen_href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(game.fen());
        var result = '*';

        if (game.in_draw()) {
            result = '1/2-1/2';
        }
        if (game.in_checkmate()) {
            result = game.turn() === 'b'? '1-0': '0-1';
        }
        var pgn_data = '[White "Human"]\n'+
                       '[Black "Moodle computer"]\n'+
                       '[Result "' + result + '"]\n'
                       + game.pgn();
        var pgn_href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(pgn_data);

        $('#download_fen').attr('href', fen_href).attr('download', 'fen.fen');
        $('#download_pgn').attr('href', pgn_href).attr('download', 'pgn.pgn');
    }

    var makeRandomMove = function() {
        var possibleMoves = game.moves();

        // Game over.
        if (possibleMoves.length === 0) return;

        var randomIndex = Math.floor(Math.random() * possibleMoves.length);
        game.move(possibleMoves[randomIndex]);
        board.position(game.fen());
        updateStatus();
        updateDownloadLinks();
        saveGame();
    };

    var updateStatus = function() {
        var status = '';
        var moveColor = (game.turn() === 'b')? language['black']: language['white'];

        if (game.in_checkmate() === true) {
            status = language['gameover'] + ', ' + moveColor + ' ' + language['isincheckmate'];
        } else if (game.in_draw() == true) {
            status = language['gameover'] + ', ' + language['drawnposition'];
        } else {
            status = moveColor + ' ' + language['tomove'];
            if (game.in_check() === true) {
                status += ', ' + moveColor + ' ' + language['isincheck'];
            }
        }
        statusEl.html(status);
    };

    var saveGame = function(){

        if(!gameRunning){
            console.log("need to start a game in order to save");
            return;
        }

        Y.io(
            M.cfg.wwwroot + "/blocks/chessblock/api/post_game_data.php", {
                method: "POST",
                data: 'gameFEN='+game.fen()+'&gamePGN=NOPE',
                on: {
                    success: function(io, o, arguments) {
                        console.log("SAVED!");
                    }
                }
            }
        );
    }

    var cfg = {
        draggable: true,
        position: 'start',
        onDragStart: onDragStart,
        onMouseoutSquare: onMouseoutSquare,
        onMouseoverSquare: onMouseoverSquare,
        onDrop: onDrop,
        onSnapEnd: onSnapEnd,
        pieceTheme: M.cfg.wwwroot + '/blocks/chessblock/chessboardjs/img/chesspieces/wikipedia/{piece}.png',
    };

    updateStatus();

    $('#newChessGame').click(function(){
        game = new Chess();
        board = new ChessBoard('board', cfg);
        $('#loadPrevChessGame').hide();
        gameRunning = true;
        updateStatus();
        putDownloadLinks();
    });

    $('#loadPrevChessGame').click(function(){
        $('#loadPrevChessGame').hide();
        // Loading from API.
        Y.io(
            M.cfg.wwwroot + "/blocks/chessblock/api/get_game_data.php", {
                method: "GET",
                on: {
                    success: function(io, o, arguments) {
                        var gameData = JSON.parse(o.response);
                        if (gameData.status && gameData.gameData.game_fen != null &&  gameData.gameData.game_fen.length > 0){
                            var cfgLoad = $.extend({}, cfg); // Copy configuration.
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
