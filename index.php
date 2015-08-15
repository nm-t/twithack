<?php
require "twitteroauth/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key = "2nsbBRuAOZDLRzWpmNe0zes18";
$consumer_secret = "DXAPr55PXruIRQMSSMvS2Y4CE3yFCfd6t3ijp7JCCb8TOJvpub";

// Database settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "twithack";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//echo "Connected successfully";

session_start();

$logged_in = false;

// Use user access tokens to authentication for posting questions or replying
if(isset($_SESSION['access_token']) && isset($_SESSION['access_token_secret'])) {
	$access_token = $_SESSION['access_token'];
	$access_token_secret = $_SESSION['access_token_secret'];
} else {
	$access_token = '';
	$access_token_secret = '';
}

$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
$content = $connection->get("account/verify_credentials");

// Base URL of the site
$base_url = "http://localhost/";
?>

<!DOCTYPE html>
<html>
	<head>
		<title>TwitHack 2015</title>

		<link href="bootstrap.min.css" rel="stylesheet">
		
		<!-- Include custom favicon -->
		<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
		
		<meta charset="UTF-8">
	</head>
	
	<body>
		<!-- Navigation bar -->
      <ul class="nav nav-tabs" role="tablist" style="margin: 10px 10px">
        <li role="presentation" class="active"><a href=index.php>Home</a></li>
        <li role="presentation"><a href="#">Profile</a></li>
        <li role="presentation"><a href="#">Messages</a></li>


<?php
// User login
if(!isset($_SESSION['access_token']) || !isset($_SESSION['access_token_secret'])) {
  if(isset($_GET['oauth_verifier'])) {
    $access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_GET['oauth_verifier'], "oauth_token" => $_GET['oauth_token']));
    $_SESSION['access_token'] = $access_token['oauth_token'];
    $_SESSION['access_token_secret'] = $access_token['oauth_token_secret'];
	$_SESSION['screen_name']  = $access_token['screen_name'];
    print("Successfully logged in as @" . $access_token['screen_name'] . ". <a href=" . $base_url . ">Click here to continue.</a>");
    header("Location: http://localhost/");
    exit;
  } else {
    $access_token = $connection->oauth("oauth/request_token", array("oauth_callback" => $base_url));

    $url = $connection->url("oauth/authenticate", array("oauth_token" => $access_token['oauth_token']));
    print("<p align=right>You are not logged in. <a href=\"" . $url . "\">Log in with Twitter!</a></p>");
  }
} else {
	// Handle log outs
	if(isset($_GET['logout'])) {
		session_destroy();
		print("<p>You have logged out. <a href=index.php>Return</a></p>");
		exit;
	}

  $userscreenname = $_SESSION['screen_name'];
  //print_r($content); // DEBUG
  print("<p>You are logged in as @" . $userscreenname . " <a href=\"?logout\">Log out.</a></p>");
  $logged_in = true;
}
?>
      </ul>

		<!-- Header -->
		<div class="container">
			<p align="center">
				<!-- <img src="TwitHackTemp.png" width=50% height=50%> -->
        <h1>AskTwitter</h1>
			</p>
		</div>
  
<?php
// Handle a user posting a question.
if(isset($_GET['submitquestion'])) {
	// Post the tweet and redirect indicating success.
	$statues = $connection->post("statuses/update", array("status" => $_GET['tweet']));
	header("Location: " . $base_url . "?success");
	exit;
} 

// Display success message if a question/answer is posted.
if(isset($_GET['success'])) {
	echo "<p>Your submission was successful!</p>";
}
?>
      
<?php
if(!isset($_GET['qid']) && $logged_in) {
?>
	<!-- Post a question -->
	<div class="container">
    <p><h2>Got a question?</h2></p>
	  
      <!-- TODO: Use jQuery for placeholder text -->
      <form>
      <input type="text" name="tweet" style="width:80%">
      <input type="submit" name="submitquestion" class="btn btn-sm btn-info" value="Post!"></input>
	  </form>
      <p>Suggested hashtags:<br>
        <!-- TODO: Insert dynamic hashtag adding -->
        <h4>
          <a href="?q=%23cat" class="label label-default">#cat</a>
          <a href="?q=%23animals" class="label label-default">#animals</a>
          <a href="?q=%23life" class="label label-default">#life</a>
          <a href="?q=%23philosophy"  class="label label-default">#philosophy</a>
        </h4>
      </p>
    </div>
<?php
}

if(!isset($_GET['qid'])) {
?>
	
		<!-- Buttons for categories -->
		<nav class="navbar">
			<div class="container">
				<h1>
					<a type="button" class="btn btn-default">Popular</a>
					<a href="?q=%3F" type="button" class="btn btn-primary">New</a>
					<a href="?q=%23qkqn" class="btn btn-success">Random</a>
			</h1>
		<!-- Search functionality -->
			<form>
			<input type="text" name="q" style="width:30%">
			<input type="submit" name="submit" class="btn btn-sm btn-info" value="Search"></input>
			</form>
			</div>
		</nav>
		
<?php } ?>		

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
    $query = '#cat'; // Placeholder query
  }
  
  // Handle upvoting of a question
  if(isset($_GET["upvote"]) && isset($_GET["voteid"])) {
	$result = $conn->query("SELECT * FROM votes WHERE postid=" . $_GET["voteid"]);
	if($result->num_rows != 1) {
		// Update the count
		$conn->query("INSERT INTO votes (postid, count, userid) VALUES (" . $_GET["voteid"] . ", 1, 0)");
	} else {
		// Increment the count on the vote
		$conn->query("UPDATE votes SET count=count+1 WHERE postid=" . $_GET["voteid"]);
	}
  }
  
  // Handle downvoting of a question
  if(isset($_GET["downvote"]) && isset($_GET["voteid"])) {
	$result = $conn->query("SELECT * FROM votes WHERE postid=" . $_GET["voteid"]);
	if($result->num_rows != 1) {
		// Update the count
		$conn->query("INSERT INTO votes (postid, count, userid) VALUES (" . $_GET["voteid"] . ", -1, 0)");
	} else {
		// Increment the count on the vote
		$conn->query("UPDATE votes SET count=count-1 WHERE postid=" . $_GET["voteid"]);
	}
  }

  print("<div class=\"container\"><table class=\"table table-striped\" style=\"width:100%\"><thead><tr><th>#</th><th>Question</th><th>Username</th><th>Tags</th></tr></thead><tbody>");

  // Get top questions matching query
  $questions = $connection->get("search/tweets", array("q" => $query, "count"=>15));
  //$count = 1;
  foreach($questions->statuses as $tweet) {
	// Get the score for each tweet
	$result = $conn->query("SELECT * FROM votes WHERE postid=" . $tweet->id);
	if($result->num_rows != 1) {
		$count  = 0;
	} else {
		$row = $result->fetch_assoc();
		$count = $row["count"];
	}
  
    print("<tr><td style=\"width:5%\">" . $count . " <a href=\"?upvote&voteid=" . $tweet->id . (isset($_GET['q']) ? ("&q=" . urlencode($_GET['q'])) : "") . "\">+</a> <a href=\"?downvote&voteid=" . $tweet->id . (isset($_GET['q']) ? ("&q=" . urlencode($_GET['q'])) : "") . "\">-</a></td><td style=\"width:50%\"><a href=\"?qid=" . $tweet->id . "&sname=" . $tweet->user->screen_name . "\">" . $tweet->text . "</a><br />");
    //$count++;
    
    //print("<form><input type=\"text\" name=\"answer_1\" size=\"50\" margin-bottom=\"5\"><br />");
    //print("<button type=\"button\" class=\"btn btn-xs btn-info\" style=\"margin: 5px 1px\">Reply</button>");
    //print("<button type=\"button\" class=\"btn btn-xs btn-default\" style=\"margin: 5px 1px\">Clear</button></td>");
    print("<td style=\"width:10%\">@" . $tweet->user->screen_name . "</td>");

    // Check if the tweet is English (enough)!

    // Delimit tweets by space character.
    $words = explode(" ", $tweet->text);
    $totalWords = sizeof($words);

    // Populate Tags column
    print("<td style=\"word-wrap: break-word; width:30%\"><h4>");
	$tagcount = 0;
    for($j = 0; $j < $totalWords; $j++){
	
      // Remove all special characters
      //$stripped = preg_replace('/[^a-z]/i', '', $words[$j]);
      //print($stripped . "<br />");

      if (substr($words[$j], 0, 1) == "#"){
		$tagcount++;
		
		// Limit the number of hash tags
		if($tagcount > 3) {
			break;
		}

          //<a href="?q=%23life" class="label label-default">#life</a>
        //print("<a href=\"?q=%23" . $words[$j] . "\" class=\"label label-default\">" . $words[$j] . "</a>");
        print("<a href=\"?q=%23" . substr($words[$j], 1) . "\" class=\"label label-default\">" . $words[$j] . "</a>&nbsp;");
        //print($words[$j] . " ");
      }
    }
    print("</h4></td>");
  }
  // Close the table up
  print("</tr></tbody></table>");
}

?>

	<div class="container">
			<p align="center">
				&copy; Team public catic void 2015
			</p>
		</div>
	</body>

</html>