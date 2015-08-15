
<!DOCTYPE html>
<html>
	<head>
		<title>TwitHack 2015</title>

		<link href="bootstrap.min.css" rel="stylesheet">
	</head>
	
	<body>

		<!-- Navigation bar -->
		<nav class="navbar navbar-default">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">TwitHack</a>
          </div>
          <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li role="separator" class="divider"></li>
                  <li class="dropdown-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </nav>

		<!-- Header -->
		<div class="container">
			<p align="center">
				<img src="TwitHackTemp.png" width=50% height=50%>
			</p>
		</div>

    <!-- Post a question -->
    <div class="container">
      <p><h2>Got a question?</h2></p>
      
      <!-- TODO: Use jQuery for placeholder text -->
      <form>
      <input type="text" name="answer_1" style="width:80%">
      <button type="button" class="btn btn-sm btn-info">Post!</button>
      <p>Suggested hashtags:<br>
        <!-- TODO: Insert dynamic hashtag adding -->
        <h4>
          <span class="label label-default">#cat</span>
          <span class="label label-default">#animals</span>
          <span class="label label-default">#life</span>
          <span class="label label-default">#philosophy</span>
        </h4>
      </p>
    </div>
		
		<!-- Buttons for categories -->
		<nav class="navbar">
			<div class="container">
				<h1>
					<button type="button" class="btn btn-default">Popular</button>
					<button type="button" class="btn btn-primary">New</button>
					<button type="button" class="btn btn-success">Random</button>
					<button type="button" class="btn btn-info">Default</button>
			</h1>
			</div>
		</nav>

		<!-- Main body -->


<?php
// Hashtable to store English dictionary
//$myFile = fopen("words.txt", "r") or die("Unable to open file!");
//while(!feof($myFile)){
//  print(fgets($myFile));
//}
//fclose($myFile);
?>


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
  $replies = $connection->get("search/tweets", array("q" => "@" . $screename, "count"=>3));
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

  print("<div class=\"container\"><table class=\"table table-striped\"><thead><tr><th>#</th><th>Question</th><th>Username</th><th>Tags</th></tr></thead><tbody>");

  // Get top questions matching query
  $questions = $connection->get("search/tweets", array("q" => $query, "count"=>3));
  $count = 1;
  foreach($questions->statuses as $tweet) {
    print("<tr><td>" . $count . "</td><td><a href=\"?qid=" . $tweet->id . "&sname=" . $tweet->user->screen_name . "\">" . $tweet->text . "</a><br />");
    $count++;

    // Check if the tweet is English (enough)!

    // Delimit tweets by space character.
    $words = explode(" ", $tweet->text);
    $totalWords = sizeof($words);
    //for($j = 0; $j < $totalWords; $j++){
      // Remove all special characters
      //$stripped = preg_replace('/[^a-z]/i', '', $words[$j]);
      //print($stripped . "<br />");
      // If the length of the word is >1
      // And the 
    //}
    
    print("<form><input type=\"text\" name=\"answer_1\" size=\"50\" margin-bottom=\"5\"><br />");
    print("<button type=\"button\" class=\"btn btn-xs btn-info\" style=\"margin: 5px 1px\">Reply</button>");
    print("<button type=\"button\" class=\"btn btn-xs btn-default\" style=\"margin: 5px 1px\">Clear</button></td>");
    print("<td>" . $tweet->user->screen_name . "</td>");
    print("<td><h4><span class=\"label label-default\">#sky</span></h4></td>");
  }
  print("</tr></tbody></table>");
}

?>

	</body>

</html>