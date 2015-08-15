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

// Get top 100 tweets matching query "where"
$statuses = $connection->get("search/tweets", array("q" => "#why", "count"=>500));

foreach($statuses->statuses as $tweet) {
	//if(substr($tweet->text, -1) == '?')
	//print($tweet->text . "<br /><br />");
	print_r($tweet);
	print("<br /><br />");
}
?>