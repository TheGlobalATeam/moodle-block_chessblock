var language = {};

var challengedUserID = -1;
var doingMultiplayer = false;
var enemyPlayerID = -1;
var isPlayer1 = true;
var mpIsMyTyrn = true;

var challengeLookRefreshTime = 1000;

var keepLookingForChallengeResponce = true;

function loadLanguage(Y, currentLanguage) {
    language = currentLanguage;
}

//make them globally available, somewhat forward declaration
var onDragStart;
var onDrop;
var onSnapEnd;
var removeHighlight;
var highlightSquare;
var onMouseoverSquare;
var onMouseoutSquare;
var putDownloadLinks;
var updateDownloadLinks;
var makeRandomMove
var updateStatus;
var saveGame;

var board;
var game;
var statusEl;
var gameRunning = false;

$( document ).ready(function() {

    game = new Chess();
    statusEl = $('#status');
    gameRunning = false;

    // Do not pick up pieces if the game is over only pick up pieces for the side to move.
    onDragStart = function(source, piece, position, orientation) {

        if(doingMultiplayer && !mpIsMyTyrn){
            return false;
        }

        if(isPlayer1){
            // Only white may move, on their turn.
            if (game.in_checkmate() === true ||
                game.in_draw() === true ||
                piece.search(/^b/) !== -1) {
                    return false;
            }
        }else{
            // Only white may move, on their turn.
            if (game.in_checkmate() === true ||
                game.in_draw() === true ||
                piece.search(/^w/) !== -1) {
                    return false;
            }
        }


    };

    onDrop = function(source, target) {
        removeHighlight();

        // See if the move is legal.
        var move = game.move({
            from: source,
            to: target,
            promotion: 'q' // NOTE: always promote to a Queen for example simplicity.
        });

        // Illegal move.
        if (move === null) {
            return 'snapback';
        }

        //my turn over
        if(doingMultiplayer){
            mpIsMyTyrn = false;
            //submit my move to the server!
            saveNewFenMP(game.fen());
        }




        updateStatus();
        updateDownloadLinks();
        if(!doingMultiplayer){
            saveGame();
            window.setTimeout(makeRandomMove, 250);
        }
    };

    // Update the board position after the piece snap for castling, en passant, pawn promotion.
    onSnapEnd = function() {
        board.position(game.fen());
    };

    removeHighlight = function(square) {
        $('#board .square-55d63').css('background', '');
    }

    highlightSquare = function(square) {
        var squareElement = $('#board .square-' + square);
        var isSqaureBlack = (squareElement.hasClass('black-3c85d') === true);
        var backgroundColor = (isSqaureBlack) ? '#696969' : '#a9a9a9';
        squareElement.css('background', backgroundColor);
    }

    onMouseoverSquare = function(square, piece) {

        // Get possible moves for this square.
        var moves = game.moves({
            square: square,
            verbose: true
        })

        if (moves.length === 0) {
            return;
        }

        //just if my turn
        if(doingMultiplayer && !mpIsMyTyrn){
            return false;
        }

        // Highlight hovered square and leagl moves.
        highlightSquare(square);
        for (var i = 0; i < moves.length; ++i) {
            highlightSquare(moves[i].to);
        }
    }

    onMouseoutSquare = function(square, piece) {
        removeHighlight();
    }

    putDownloadLinks = function() {
        var fen_link = '<a id="download_fen">' + language['download'] + ' FEN</a>'
        var pgn_link = '<a id="download_pgn">' + language['download'] + ' PGN</a>'
        $('#download_fen_parent').html(fen_link);
        $('#download_pgn_parent').html(pgn_link);
        updateDownloadLinks();
    }

    updateDownloadLinks = function() {
        var fen_href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(game.fen());
        var result = '*';

        if (game.in_draw()) {
            result = '1/2-1/2';
        }
        if (game.in_checkmate()) {
            result = game.turn() === 'b' ? '1-0' : '0-1';
        }
        var pgn_data = '[White "Human"]\n[Black "Moodle computer"]\n[Result "' + result + '"]\n' + game.pgn();
        var pgn_href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(pgn_data);

        $('#download_fen').attr('href', fen_href).attr('download', 'fen.fen');
        $('#download_pgn').attr('href', pgn_href).attr('download', 'pgn.pgn');
    }

    makeRandomMove = function() {
        var possibleMoves = game.moves();

        // Game over.
        if (possibleMoves.length === 0) {
            return;
        }

        var randomIndex = Math.floor(Math.random() * possibleMoves.length);
        game.move(possibleMoves[randomIndex]);
        board.position(game.fen());
        updateStatus();
        updateDownloadLinks();
        saveGame();
    };

    updateStatus = function() {
        var status = '';
        var moveColor = (game.turn() === 'b') ? language['black'] : language['white'];

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

    saveGame = function(){

        if(!gameRunning){
            console.log("need to start a game in order to save");
            return;
        }

        Y.io(
            M.cfg.wwwroot + "/blocks/chessblock/api/post_game_data.php", {
                method: "POST",
                data: 'gameFEN=' + game.fen() + '&gamePGN=NOPE',
                on: {
                    success: function(io, o, arguments) {
                        //console.log("SAVED!");
                    }
                }
            }
        );
    }



    updateStatus();

    $('#newChessGame').click(function(){
        startChessGame();
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
                            $(window).resize(board.resize);
                            gameRunning = true;
                        }
                    }
                }
            }
        );
    });


    $('#openMultiplayer').click(function(){

        // $('#loadPrevChessGame').hide();
        // $('#loadPrevChessGame').hide();
        // Loading from API.
        Y.io(
            M.cfg.wwwroot + "/blocks/chessblock/api/get_online_users.php", {
                method: "GET",
                on: {
                    success: function(io, o, arguments) {

                        var gameData = JSON.parse(o.response);
                        let dataContent = "<p>Select player to challenge:</p>";

                        for(let i = 0; i < gameData.userList.length; i ++){
                            //console.dir(gameData.userList[i].id);
                            dataContent += '<br><button onclick="challengePlayerByID('+gameData.userList[i].id+')">Challenge '+gameData.userList[i].username+'</button>';
                        }

                        $('#onlineUsersListContainer').html(dataContent);

                    }
                }
            }
        );


    });







});

function startChessGame(usingFen = false){

    var chessCfg = {
        draggable: true,
        orientation: 'white',
        position: 'start',
        onDragStart: onDragStart,
        onMouseoutSquare: onMouseoutSquare,
        onMouseoverSquare: onMouseoverSquare,
        onDrop: onDrop,
        onSnapEnd: onSnapEnd,
        pieceTheme: M.cfg.wwwroot + '/blocks/chessblock/chessboardjs/img/chesspieces/wikipedia/{piece}.png',
    };

    if(usingFen != false){
        console.log("using pos");
        chessCfg.position = usingFen
    }

    if(doingMultiplayer){

        if(isPlayer1){
            chessCfg.orientation = 'white';
        }else{
            chessCfg.orientation = 'black';
        }

        //debug as not working default both to white
        chessCfg.orientation = 'white';

    }

    board = new ChessBoard('board', chessCfg);
    //console.log(board.fen());
    if(usingFen != false){
        game = new Chess(usingFen);
    }else{
        game = new Chess();
    }
    //console.log(game.fen());
    $('#loadPrevChessGame').hide();
    gameRunning = true;
    updateStatus();
    $(window).resize(board.resize);
    putDownloadLinks();
}




function challengePlayerByID(playerID){ //TDO add username here

    $('#loadingDiv').show();
    $('#buttonsContainer').hide();
    $('#gameInfoCointainer').hide();

    $('#onlineUsersListContainer').html("<p>Loading</p>");

    challengedUserID = playerID;
    enemyPlayerID = playerID;

    Y.io(
        M.cfg.wwwroot + "/blocks/chessblock/api/post_chess_challenge.php", {
            method: "POST",
            data: 'challengedUserID=' + playerID ,
            on: {
                success: function(io, o, arguments) {
                    var challengeResponce = JSON.parse(o.response);
                    // console.dir(challengeResponce);
                    // console.log("SAVED!");
                    $('#onlineUsersListContainer').html("<p>You have challenged "+playerID+"</p>");



                    lookForResponce();

                }
            }
        }
    );



    console.log("I challenge: " + playerID);

}



function lookForResponce(){


    Y.io(
        M.cfg.wwwroot + "/blocks/chessblock/api/chess_challenge_status.php?challengedUserID="+challengedUserID, {
            method: "GET",
            on: {
                success: function(io, o, arguments) {
                    var gameData = JSON.parse(o.response);
                    // console.log("responce lookForResponce");
                    // console.dir(gameData);
                    if (gameData.status){

                        if (gameData.challengeAccepted){
                            keepLookingForChallengeResponce = false;
                            startMultiplayerMatch(true);
                        }

                    }
                }
            }
        }
    );

    if(keepLookingForChallengeResponce){
        setTimeout(lookForResponce, challengeLookRefreshTime);
    }

}


//also called by multiplayerNotificatin by player 2
function startMultiplayerMatch(isFirstPlayer){

    //safequard against race condition as only the first call will be accepted
    if(doingMultiplayer){
        return;
    }
    doingMultiplayer = true;
    keepLookingForChallenge = false;

    isPlayer1 = isFirstPlayer;
    if(isFirstPlayer){

        $('#loadingDiv').hide();
        $('#buttonsContainer').show();
        $('#gameInfoCointainer').show();

        $('#onlineUsersListContainer').html("<p>In multiplayer</p>");

        console.log("Starting MP FirstPlayer");
    }else{
        mpIsMyTyrn = false;
        console.log("Starting MP SecoundPlayer");

        //TODO start looking for other players move
    }

    startChessGame();
    lookForOtherPlayersMoves();


}


function saveNewFenMP(fen){

    var isPlayer1Number = 1;
    if(!isPlayer1){
        isPlayer1Number = 0;
    }

    Y.io(
        M.cfg.wwwroot + "/blocks/chessblock/api/chess_mp_moves.php?isPlayer1="+isPlayer1Number+"&otherPlayerID="+enemyPlayerID, {
            method: "POST",
            data: 'newFen=' + fen,
            on: {
                success: function(io, o, arguments) {
                    //console.log(o.response);
                    var responce = JSON.parse(o.response);
                    //console.dir(responce);

                    if(responce.status){
                        //console.log("SAVED!");
                    }
                }
            }
        }
    );

}

function lookForOtherPlayersMoves(){

    var isPlayer1Number = 1;
    if(!isPlayer1){
        isPlayer1Number = 0;
    }

    Y.io(
        M.cfg.wwwroot + "/blocks/chessblock/api/chess_mp_moves.php?isPlayer1="+isPlayer1Number+"&otherPlayerID="+enemyPlayerID, {
            method: "GET",
            on: {
                success: function(io, o, arguments) {
                    var returnJson = JSON.parse(o.response);
                    if (returnJson.status){

                        if(returnJson.gameData.game_fen != game.fen()){
                            //new fen!
                            var oldFen = game.fen();
                            var newFen = returnJson.gameData.game_fen;

                            mpIsMyTyrn = true;
                            startChessGame(newFen);

                        }

                    }
                }
            }
        }
    );


    setTimeout(lookForOtherPlayersMoves, challengeLookRefreshTime);

}
