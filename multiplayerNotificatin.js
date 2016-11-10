var canUseNotifications = false;
var lastChallengeTime = -1;

// Let's check if the browser supports notifications
if (!("Notification" in window)) {
	console.log("This browser does not support desktop notification");
}else if (Notification.permission === "granted") { //already granted

	canUseNotifications = true;

}else if (Notification.permission !== 'denied') {

	console.log("Notification ask");

	// we need to ask the user for permission
	Notification.requestPermission(function (permission) {
		// If the user accepts, let's create a notification
		if (permission === "granted") {
			console.log("Notification allowed");
			canUseNotifications = true;
		}
	});
}

if (canUseNotifications) {

	lookForChallenges();

}

function lookForChallenges(){

	Y.io(
		M.cfg.wwwroot + "/blocks/chessblock/api/get_chess_challenges.php?lastChallengeTime="+lastChallengeTime, {
			method: "GET",
			on: {
				success: function(io, o, arguments) {
					var gameData = JSON.parse(o.response);
					console.log("responce");
					console.dir(gameData);
					if (gameData.status){
						lastChallengeTime = gameData.challenges[0].challenged_time;
						console.log("yess!" + lastChallengeTime);
						addNewNotification(gameData.challenges[0].challenger_user_id)

					}
				}
			}
		}
	);

	setTimeout(lookForChallenges, 3000);

}

function addNewNotification(challengerUserID){

	var options = {
			body: "Challenged by userID: "+challengerUserID,
			icon: '/moodle/blocks/chessblock/Chess-Game.png',
			tag:  "#LiveToLearn"
		}

		var title = "New chess Challenge!";
		var notification = new Notification(title, options);

		notification.onclick = function() {
			notification.close();

			console.log("Clicked!");

		}

}
