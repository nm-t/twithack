<?php
require "twitteroauth/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

session_start();

// App-specific access tokens
$consumer_key = "2nsbBRuAOZDLRzWpmNe0zes18";
$consumer_secret = "DXAPr55PXruIRQMSSMvS2Y4CE3yFCfd6t3ijp7JCCb8TOJvpub";

// Use user access tokens to authentication for posting questions or replying
if(isset($_SESSION['access_token']) && isset($_SESSION['access_token_secret'])) {
	$access_token = $_SESSION['access_token'];
	$access_token_secret = $_SESSION['access_token_secret'];
} else {
	$access_token = '';
	$access_token_secret = '';
}

// Instantiate authentication framework for twitter API
$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
$content = $connection->get("account/verify_credentials");

// User login
if(!isset($_SESSION['access_token']) || !isset($_SESSION['access_token_secret'])) {
	if(isset($_GET['oauth_verifier'])) {
		$access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_GET['oauth_verifier'], "oauth_token" => $_GET['oauth_token']));
		$_SESSION['access_token'] = $access_token['oauth_token'];
		$_SESSION['access_token_secret'] = $access_token['oauth_token_secret'];
		print("Successfully logged in as @" . $access_token['screen_name'] . ". <a href='twitter.php'>Click here to continue.</a>");
		header("Location: http://localhost/twitter.php");
		exit;
	} else {
		$access_token = $connection->oauth("oauth/request_token", array("oauth_callback" => "http://localhost/twitter.php"));
		//print_r($access_token);
		print("<br />");
		$url = $connection->url("oauth/authenticate", array("oauth_token" => $access_token['oauth_token']));
		print("<a href=\"" . $url . "\">You are not logged in. Log in with twitter!</a>");
		print("<br />");
	}
} else {
	$userscreenname = $content->screen_name;
	//print_r($content); // DEBUG
	print("<p>You are logged in as @" . $userscreenname . "</p>");
}

// Get http request data to determine if we're viewing questions or replies
if (isset($_GET['qid'])) {	
	$questionid = $_GET['qid'];
	$screename = $_GET['sname'];
	
	// Handle a submitted answer to a question.
	if(isset($_GET['submit'])) {
		$statues = $connection->post("statuses/update", array("status" => "@" . $screename  . " " . $_GET['tweet'], "in_reply_to_status_id" => $questionid));
		header("Location: http://localhost/twitter.php?qid=" . $questionid . "&sname=" . $screename . "&success");
		exit;
	}
	
	if(isset($_GET['success'])) {
		print("<p>Your answer has successfully been submitted</p>");
	}
	
	print("<p>Viewing replies to question " . $questionid . "</p>");
	
	// Get the original question
	$question = $connection->get("statuses/show", array("id" => $questionid));
	
	print("<b>" . $question->user->screen_name . "</b>:");
	print("<p><i>" . $question->text . "</i></p><hr />");	
	
	// Get and display replies to a particular question
	$replycount = 0;
	$replies = $connection->get("search/tweets", array("q" => "@" . $screename, "count"=>100));
	foreach($replies->statuses as $reply) {	
		if($reply->in_reply_to_status_id == $questionid) {
			$replycount++;
			print("<p>" . $reply->user->screen_name . ": " . $reply->text . "</p><hr >");
		}
	}
	
	print("<p>Answer:</p><form><br><input type=\"text\" name=\"tweet\"><input type=\"hidden\" name=\"sname\" value=\"" . $screename . "\"><input type=\"hidden\"  name=\"qid\" value=\"" . $questionid . "\"><br /><br /><input type=\"submit\" name=\"submit\"></input></form><hr />");
	
	// Display message if there are no replies
	if($replycount == 0) {
		print("<p>No replies yet. Write one!</p>");
		exit;
	}
	
} else {
	// Handle posted response if there is one
	if(isset($_GET['submit'])) {
		$statues = $connection->post("statuses/update", array("status" => $_GET['tweet']));
		print("<p>Your question was successfully submitted!</p>");
	} else {
		// Form thing for people to post questions
		print("Ask a question:<br />");
		print("<form><br><input type=\"text\" name=\"tweet\"><br /><br /><input type=\"submit\" name=\"submit\"></input></form><hr />");
	}

	// Get query if there is one?
	if(isset($_GET['q'])) {
		$query = $_GET['q'];
	} else {
		$query = '?'; // Placeholder query
	}

	// Get top questions matching query
	$questions = $connection->get("search/tweets", array("q" => $query, "count"=>10));
	foreach($questions->statuses as $tweet) {
		print($tweet->user->screen_name . " sez (ID: " .  $tweet->id. "):<br />");
		print("<p><a href=\"?qid=" . $tweet->id . "&sname=" . $tweet->user->screen_name . "\">" . $tweet->text . "</a></p>");
		
		//print_r($tweet);

		print("<br /><hr />");
	}
}

?>