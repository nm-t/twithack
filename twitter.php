<?php
require "twitteroauth/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key = "2nsbBRuAOZDLRzWpmNe0zes18";
$consumer_secret = "DXAPr55PXruIRQMSSMvS2Y4CE3yFCfd6t3ijp7JCCb8TOJvpub";
$access_token = "3315621726-89FeofhsUwmUoArQmMcCi6PnEUVxe1FzNErYcqv";
$access_token_secret = "aXqK7VxF2YRKyVNPMF4ZPvgrHiZ970hpc5ZnzzV2PQ3i2";

$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);

$content = $connection->get("account/verify_credentials");
//$statues = $connection->get("statuses/home_timeline", array("count" => 25, "exclude_replies" => true));
//$statues = $connection->post("statuses/update", array("status" => "hello world"));

//print_r($statuses->statuses[0]->text);

// Get http request data to determine if we're viewing questions or replies

if (isset($_GET['qid'])) {	
	$questionid = $_GET['qid'];
	$screename = $_GET['sname'];
	
	print("<p>Viewing replies to question " . $questionid . "</p>");
	
	// Get the original question
	$question = $connection->get("statuses/show", array("id" => $questionid));
	
	print("<b>" . $question->user->screen_name . "</b>:");
	print("<p><i>" . $question->text . "</i></p><hr />");	
	
	// Get and display replies to a particular question
	$replycount = 0;
	$replies = $connection->get("search/tweets", array("q" => "@" . $screename, "count"=>5));
	foreach($replies->statuses as $reply) {	
		if($reply->in_reply_to_status_id == $questionid) {
			$replycount++;
			print("<p>" . $reply->user->screen_name . ": " . $reply->text . "</p><hr >");
		}
	}
	
	// Display message if there are no replies
	if($replycount == 0) {
		print("<p>No replies yet. Write one!</p>");
	}
	
} else {
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